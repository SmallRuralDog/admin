<?php

namespace smallruraldog\admin\component\form;

use Exception;
use Illuminate\Database\Eloquent\Relations;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Respect\Validation\ChainedValidator;
use Respect\Validation\Validator;
use support\Db;
use support\Response;

trait FormResource
{
    protected array $inputs = [];
    protected array $ignored = [];
    protected array $updates = [];
    protected array $relations = [];

    private bool $isEdit = false;
    private bool $isQuickEdit = false;
    private mixed $editKey;
    private mixed $editData;
    protected array $addRules = [];
    protected array $addRulesMessages = [];


    public function __get($name)
    {
        return $this->input($name);
    }

    public function __set($name, $value)
    {
        return Arr::set($this->inputs, $name, $value);
    }

    /**
     * 删除无需保存的字段
     */
    public function deleteInput(string $name): void
    {
        Arr::forget($this->inputs, $name);
    }

    /**
     * 添加自定义验证规则
     */
    public function setRules(array $rules): self
    {
        foreach ($rules as $key => $rule) {
            abort_if(!($rule instanceof ChainedValidator), 400, '规则必须是ChainedValidator');
        }
        $this->addRules = Arr::collapse([$rules, $this->addRules]);
        return $this;
    }

    /**
     * 验证数据
     */
    private function validatorData($data)
    {
        $rules = [];
        /* @var Item $item */
        foreach ($this->items as $item) {
            /**@var $itemRule ChainedValidator */
            $itemRule = $item->getRule();

            $itemRule->setName($item->getLabel());
            $rules[$item->getName()] = $itemRule;
        }

        //合并自定义规则
        $rules = Arr::collapse([$rules, $this->addRules]);

        //如果是快捷编辑，只提取编辑字段的规则
        if ($this->isQuickEdit) {
            $rules = collect($rules)->filter(function ($item, $key) use ($data) {
                return in_array($key, collect($data)->keys()->toArray());
            })->toArray();
        }

        /**@var Validator $rule */
        foreach ($rules as $key => $rule) {
            if ($rule->getRules()) {
                try {
                    $rule->check(data_get($data, $key));
                } catch (Exception $exception) {
                    return json([
                        'status' => 422,
                        "msg" => $exception->getMessage(),
                        "errors" => [
                            $key => $exception->getMessage()
                        ]
                    ]);
                }
            }
        }

        $this->callValidatorEnd($data);


        return null;


    }

    private function input($key, $value = null)
    {
        if (is_null($value)) {
            return Arr::get($this->inputs, $key);
        }
        return Arr::set($this->inputs, $key, $value);
    }

    /**
     * 预处理数据
     * @param array $data
     * @return void
     */
    protected function prepare(array $data = []): void
    {

        //处理要过滤的字段
        $this->inputs = array_merge($this->removeIgnoredFields($data), $this->inputs);
        //保存前钩子
        $this->callSaving();

        //处理关联字段
        $this->relations = $this->getRelationInputs($this->inputs);
        $this->updates = Arr::except($this->inputs, array_keys($this->relations));

        $items = $this->getItems();
        /**@var Item $item */
        foreach ($items as $item) {
            if (!collect($this->updates)->has($item->getName())) continue;
            $component = $item->render();
            if (method_exists($component::class, 'setValue')) {
                $value = data_get($this->updates, $item->getName());
                data_set($this->updates, $item->getName(), $component->setValue($value));
            }
        }
    }

    /**
     * 预处理新增数据
     */
    protected function prepareInsert(array $inserts): array
    {
        $prepared = [];
        $formColumns = collect($this->getItems())->map(fn($item) => $item->getName())->toArray();
        $dbColumns = $this->getTableColumns();
        $columns = array_merge($formColumns, $dbColumns);
        //数组去除重复项
        $columns = array_unique($columns);
        foreach ($inserts as $key => $value) {
            if (in_array($key, $columns)) {
                Arr::set($prepared, $key, $value);
            }
        }
        return $prepared;
    }

    private function getTableColumns(): array
    {
        return Db::schema()->getColumnListing($this->model()->getTable());
    }

    /**
     * 预处理更新数据
     * @param array $updates
     * @param bool $oneToOneRelation
     * @return array
     */
    protected function prepareUpdate(array $updates, bool $oneToOneRelation = false): array
    {

        $prepared = [];
        $formColumns = collect($this->getItems())->map(fn($item) => $item->getName())->toArray();
        $dbColumns = $this->getTableColumns();
        $columns = array_merge($formColumns, $dbColumns);

        //数组去除重复项
        $columns = array_unique($columns);
        foreach ($updates as $key => $value) {
            if (in_array($key, $columns)) {
                Arr::set($prepared, $key, $value);
            }
        }
        return $prepared;
    }

    /**
     * 忽略字段
     */
    public function ignored(string $field): self
    {
        $this->ignored[] = $field;
        return $this;
    }

    /**
     * 过滤需要忽略的字段
     * @param $input
     * @return array
     */
    protected function removeIgnoredFields($input): array
    {
        Arr::forget($input, $this->ignored);
        return $input;
    }

    /**
     * 获取关联数据
     * @param array $inputs
     * @return array
     */
    protected function getRelationInputs(array $inputs = []): array
    {

        $relations = [];
        foreach ($inputs as $column => $value) {
            $column = Str::camel($column);
            if (!method_exists($this->model, $column)) {
                continue;
            }
            $relation = call_user_func([$this->model, $column]);
            if ($relation instanceof Relation) {
                $relations[$column] = $value;
            }
        }
        return $relations;
    }

    /**
     * 获取关联模型
     * @return array
     */
    private function getRelations(): array
    {
        $relations = [];
        $columns = collect($this->items)->map(function (Item $item) {
            return $item->getName();
        })->toArray();
        foreach (Arr::flatten($columns) as $column) {
            if (Str::contains($column, '.')) {
                [$relation] = explode('.', $column);
                if (method_exists($this->model, $relation) && $this->model()->$relation() instanceof Relation) {
                    $relations[] = $relation;
                }
            } elseif (method_exists($this->model, $column)) {
                $relations[] = $column;
            }
        }

        return array_unique($relations);
    }

    private function updateRelation($relationsData): void
    {
        foreach ($relationsData as $name => $values) {
            if (!method_exists($this->model, $name)) {
                continue;
            }
            $relation = $this->model->$name();

            //$oneToOneRelation = $relation instanceof Relations\HasOne || $relation instanceof Relations\MorphOne || $relation instanceof Relations\BelongsTo;

            $prepared = [$name => $values];
            if (empty($prepared)) {
                continue;
            }

            switch (true) {
                case $relation instanceof Relations\BelongsToMany:
                case $relation instanceof Relations\MorphToMany:
                    if (isset($prepared[$name])) {
                        $relation->sync($prepared[$name]);
                    }
                    break;
                case $relation instanceof Relations\HasOne:

                    $related = $this->model->$name;
                    if (is_null($related)) {
                        $related = $relation->getRelated();
                        $qualifiedParentKeyName = $relation->getQualifiedParentKeyName();
                        $localKey = Arr::last(explode('.', $qualifiedParentKeyName));
                        $related->{$relation->getForeignKeyName()} = $this->model->{$localKey};
                    }
                    foreach ($prepared[$name] as $column => $value) {
                        $related->setAttribute($column, $value);
                    }
                    $related->save();
                    break;
                case $relation instanceof Relations\BelongsTo:
                case $relation instanceof Relations\MorphTo:

                    $parent = $this->model->$name;
                    if (is_null($parent)) {
                        $parent = $relation->getRelated();
                    }
                    foreach ($prepared[$name] as $column => $value) {
                        $parent->setAttribute($column, $value);
                    }
                    $parent->save();
                    $foreignKeyMethod = 'getForeignKeyName';
                    if (!$this->model->{$relation->{$foreignKeyMethod}()}) {
                        $this->model->{$relation->{$foreignKeyMethod}()} = $parent->getKey();
                        $this->model->save();
                    }
                    break;
                case $relation instanceof Relations\MorphOne:
                    $related = $this->model->$name;
                    if ($related === null) {
                        $related = $relation->make();
                    }
                    foreach ($prepared[$name] as $column => $value) {
                        $related->setAttribute($column, $value);
                    }
                    $related->save();
                    break;
                case $relation instanceof Relations\HasMany:
                case $relation instanceof Relations\MorphMany:

                    foreach ($prepared[$name] as $related) {
                        /** @var Relations\Relation $relation */
                        $relation = $this->model()->$name();

                        $keyName = $relation->getRelated()->getKeyName();

                        $instance = $relation->findOrNew(Arr::get($related, $keyName));

                        //处理已删除的关联
                        if ($related[static::REMOVE_FLAG_NAME] == 1) {
                            $instance?->delete();
                            continue;
                        }
                        Arr::forget($related, static::REMOVE_FLAG_NAME);
                        //过滤不存在的字段
                        foreach ($related as $key => $value) {
                            if (Db::schema()->hasColumn($instance?->getTable(), $key)) {
                                $instance?->setAttribute($key, $value);
                            }
                        }
                        $instance?->save();
                    }
                    break;
            }
        }
    }

    /**
     * 新增数据
     */
    public function store()
    {
        try {
            //提交事件
            $this->callSubmitted();
            $data = $this->request->all();
            //验证数据
            $check = $this->validatorData($data);
            if ($check) {
                return $check;
            }
            //预处理数据
            $this->prepare($data);
            $inserts = $this->prepareInsert($this->updates);
            foreach ($inserts as $key => $value) {
                $this->model()->setAttribute($key, $value);
            }
            $this->model()->save();
            $this->updateRelation($this->relations);
            $item = $this->model();
            $this->callSaved();
            return jsonData($item);
        } catch (Exception $exception) {
            return amisError($exception->getMessage());
        }
    }

    /**
     * 编辑数据
     */
    public function edit($id): self
    {

        $this->callEditing($id);
        $this->editKey = $id;
        $this->isEdit = true;
        $this->initEditData();
        return $this;
    }

    /**
     * 编辑数据
     * @return void
     */
    private function initEditData(): void
    {

        $setWith = collect($this->builder->getEagerLoads())->keys()->toArray();
        //排除已有的with
        collect($this->getRelations())->each(function ($relation) use ($setWith) {
            if (!in_array($relation, $setWith)) {
                $this->builder->with($relation);
            }
        });
        $this->editData = $this->builder->findOrFail($this->editKey);


        $this->prepareEditData($this->editData);

        $this->callEdiData($this->editData);
    }

    /**
     * 编辑数据预处理
     * @param $editData
     * @return void
     */
    private function prepareEditData($editData)
    {
        $items = $this->getItems();
        /**@var Item $item */
        foreach ($items as $item) {
            $component = $item->render();
            if (method_exists($component::class, 'getValue')) {
                $value = data_get($editData, $item->getName());
                data_set($editData, $item->getName(), $component->getValue($value));
            }
        }
        $this->editData = $editData;
    }

    /**
     * 删除编辑初始化字段
     */
    public function deleteEditData(string $name): void
    {
        Arr::forget($this->editData, $name);
    }

    /**
     * 获取编辑数据
     */
    public function getEditData(): mixed
    {
        return $this->editData;
    }

    /**
     * 更新单条数据
     */
    public function update($id, $data = null)
    {
        try {
            $data = $data ?? $this->request->all();
            $this->model = $this->builder->findOrFail($id);
            return $this->_update($data) ?: amisSuccess("更新成功");
        } catch (Exception $exception) {
            return amisError($exception->getMessage());
        }
    }

    /**
     * 快捷更新单挑数据某些字段
     */
    public function quickItemUpdate(): Response
    {
        try {
            $allData = $this->request->all();
            $editData = $this->request->input('quickEdit');
            $key = data_get($allData, $this->getPrimaryKey());
            return $this->update($key, $editData);
        } catch (Exception $exception) {
            return amisError($exception->getMessage());
        }
    }

    /**
     * 快捷批量更新某些字段
     */
    public function quickUpdate(): Response
    {
        try {
            $this->isQuickEdit = true;
            $data = (array)$this->request->input('rowsDiff');
            $ids = collect($data)->pluck($this->getPrimaryKey())->toArray();
            foreach ($this->builder->whereIn($this->getPrimaryKey(), $ids)->cursor() as $item) {
                $this->inputs = [];
                $updateData = collect($data)->filter(function ($value) use ($item) {
                    return data_get($value, $this->getPrimaryKey()) == $item->getKey();
                })->first();
                $this->model = $item;
                $check = $this->_update($updateData);
                if ($check) {
                    return $check;
                }
            }
            return amisSuccess("更新成功");
        } catch (Exception $exception) {
            return amisError($exception->getMessage());
        }
    }

    /**
     * 更新数据入库
     */
    private function _update($data)
    {


        $this->callSubmitted();
        //验证数据
        $check = $this->validatorData($data);
        if ($check) {
            return $check;
        }
        //预处理数据
        $this->prepare($data);
        $updates = $this->prepareUpdate($this->updates);
        foreach ($updates as $key => $value) {
            $this->model->setAttribute($key, $value);
        }
        Db::transaction(function () {
            $this->model->save();
            $this->updateRelation($this->relations);
        });
        $this->callSaved();
    }

    /**
     * 删除数据
     */
    public function destroy($ids)
    {
        try {
            $this->callDeleting($ids);
            $relations = $this->getRelations();
            $ids = explode(',', $ids);
            $items = $this->getItems();
            foreach ($this->builder->with($relations)->whereIn($this->getPrimaryKey(), $ids)->lazy() as $item) {
                $this->model = $item;
                /**@var Item $i */
                foreach ($items as $i) {
                    if (!collect($item)->has($i->getName())) continue;
                    $component = $i->render();
                    if (method_exists($component::class, 'onDelete')) {
                        $value = data_get($item, $i->getName());
                        $component->onDelete($value);
                    }
                }
                //删除关联模型数据
                $this->deleteRelation($relations);
                $item->delete();
            }

            $this->callDeleted();
            return jsonData("删除成功");
        } catch (Exception $exception) {
            return amisError($exception->getMessage());
        }
    }

    private function deleteRelation($relations): void
    {
        foreach ($relations as $name) {
            if (!method_exists($this->model, $name)) {
                continue;
            }
            $relation = $this->model->$name();
            if ($relation instanceof Relations\HasOne) {
                $relation->delete();
            }
        }
    }

}