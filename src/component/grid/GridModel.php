<?php

namespace smallruraldog\admin\component\grid;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use smallruraldog\admin\component\Grid;


class GridModel
{
    protected Builder $builder;
    protected Model $model;
    protected Grid $grid;

    public function __construct($builder, Grid $grid)
    {
        $this->builder = $builder;
        $this->grid = $grid;
        $this->model = $builder->getModel();
    }

    /**
     * @return Builder
     */
    public function getBuilder(): Builder
    {
        return $this->builder;
    }

    /**
     * @return Model
     */
    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * 获取关联模型
     * @return array
     */
    private function getRelations(): array
    {
        $relations = [];
        $columns = collect($this->grid->getColumns())->map(function (Column $item) {
            return $item->getName();
        })->toArray();

        foreach (Arr::flatten($columns) as $column) {
            if (Str::contains($column, '.')) {
                [$relation] = explode('.', $column);
                if (method_exists($this->model, $relation) && $this->model->$relation() instanceof Relation) {
                    $relations[] = $relation;
                }
            } elseif (method_exists($this->model, $column)) {
                $relations[] = $column;
            }
        }
        return array_unique($relations);
    }

    /**
     * 设置排序
     * @return void
     */
    protected function setOrder(): void
    {
        $orderBy = $this->grid->getRequest()->input('orderBy');
        $orderDir = $this->grid->getRequest()->input('orderDir');
        if ($orderBy && $orderDir) {
            $this->builder->orderBy($orderBy, $orderDir);
        }
    }

    /**
     * 设置查询条件
     * @return void
     */
    protected function setWhere(): void
    {
        $where = (array)$this->grid->getRequest()->input('search');
        if (count($where) <= 0) return;
        $filterField = $this->grid->getFilterField();
        foreach ($where as $key => $value) {
            if (!isset($value)) continue;
            $field = collect($filterField)->filter(function ($field) use ($key) {
                return $field['field'] === $key;
            })->first();
            if (!$field) continue;

            //需要查询的字段
            $fieldName = $field['field'];
            switch ($field['type']) {
                case 'where':
                    $fun = $field['fun'];
                    $fun($this->builder, $value);
                    break;
                case 'eq':
                    $this->builder->where($fieldName, $value);
                    break;
                case 'like':
                    $this->builder->where($fieldName, 'like', "%$value%");
                    break;
                case 'between':
                    $this->builder->whereBetween($fieldName, $value);
                    break;
                case 'in':
                    $this->builder->whereIn($fieldName, $value);
                    break;
                case 'notIn':
                    $this->builder->whereNotIn($fieldName, $value);
                    break;
            }
        }
    }

    /**
     * 自动预加载
     */
    protected function autoWith(): void
    {
        $setWith = collect($this->builder->getEagerLoads())->keys()->toArray();
        //排除自定义的with
        collect($this->getRelations())->each(function ($relation) use ($setWith) {
            if (!in_array($relation, $setWith)) {
                $this->builder->with($relation);
            }
        });
    }

    /**
     * 构建数据
     * @return array
     */
    public function buildData(): array
    {
        return $this->get();
    }

    /**
     * 数据预处理
     */
    protected function prepareData(&$data): void
    {
        $columns = $this->grid->getColumns();
        /**@var Column $column */
        foreach ($columns as $column) {
            $component = $column->render();
            if (method_exists($component::class, 'getValue')) {
                foreach ($data as &$item) {
                    $value = data_get($item, $column->getName());
                    data_set($item, $column->getName(), $component->getValue($value));
                }
            }
        }
    }

    protected function get(): array
    {
        $this->autoWith();
        $this->setWhere();
        $this->setOrder();
        if ($this->grid->isLoadDataOnce()) {
            $pageData = $this->builder->get();
            $this->prepareData($pageData);
            $this->callData($pageData);
            return $this->toData($pageData);
        }
        $prePage = (int)$this->grid->getRequest()->input('pageSize', 10);
        $pageData = $this->builder->paginate($prePage);
        $items = $pageData->items();
        $this->prepareData($items);
        $items = $this->callData($items);
        return [
            'items' => $items,
            'total' => $pageData->total(),
        ];
    }

    private function callData($items)
    {
        $callRows = $this->grid->callRows;
        $callRow = $this->grid->callRow;
        if ($callRows) {
            $items = $callRows($items);
        }
        if ($callRow) {
            foreach ($items as $key => $item) {
                $items[$key] = $callRow($item);
            }
        }
        return $items;
    }

    private function toData($data)
    {
        if ($this->grid->isToTree()) {
            return arr2tree($data, $this->grid->getToTreeKey(), $this->grid->getToTreeParentKey(), $this->grid->getToTreeChildrenName());
        }
        return $data;
    }
}