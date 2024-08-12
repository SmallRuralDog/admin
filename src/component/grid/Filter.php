<?php

namespace smallruraldog\admin\component\grid;

use Closure;
use smallruraldog\admin\component\form\Item;
use smallruraldog\admin\renderer\Button;
use smallruraldog\admin\renderer\Flex;
use smallruraldog\admin\renderer\form\AmisForm;
use smallruraldog\admin\renderer\GridSchema;


class Filter extends AmisForm
{
    private array $filterItems = [];
    private array $filterField = [];
    private array $defaultValue = [];

    private bool $isDisableReset = false;


    protected string $addColumnsClass = "";

    public string $labelWidth = "60px";

    public function __construct()
    {
        parent::__construct();
        $this->submitText("");
        $this
            ->mode("horizontal")
            ->labelAlign("left")
            ->wrapWithPanel(false)
            ->className('pb-4 pt-2 mx-1 admin-grid-filter');
    }

    protected function addItem($name = '', $label = ''): Item
    {
        $searchName = "search.$name";
        $label = str_replace("：", ":", $label);

        if (!str_ends_with($label, ":")) {
            $label .= ":";
        }

        $label = str_replace(":", "", $label);

        $item = new Item($searchName, $label);
        $item->size('full');
        $item->placeholder("请输入" . $label);
        $this->filterItems[] = $item;
        return $item;
    }

    private function addField($field, $type, Closure $fun = null): void
    {
        $item = [
            'field' => $field,
            'type' => $type,
            'fun' => $fun
        ];
        $this->filterField[] = $item;
    }

    public function getFilterField(): array
    {
        return $this->filterField;
    }

    /**
     * 设置搜索表单label宽度 例如 20% 默认25%
     * @param string $labelWidth
     * @return $this
     */
    public function setLabelWidth(string $labelWidth): Filter
    {
        $this->labelWidth = $labelWidth;
        return $this;
    }

    /**
     * 设置搜索表单Grid布局额外的class
     * @param string $addColumnsClass
     * @return $this
     */
    public function setAddColumnsClass(string $addColumnsClass): Filter
    {
        $this->addColumnsClass = $addColumnsClass;
        return $this;
    }

    public function renderBody()
    {
        $items = [];
        if (count($this->filterField) > 0) {
            foreach ($this->filterItems as $item) {
                /**@var Item $item */
                $items[] = $item->render();
            }

        }

        $cols = collect([
            'xl4:grid-cols-5',
            'xl3:grid-cols-4',
            'xl2:grid-cols-4',
            'xl:grid-cols-3',
            'lg:grid-cols-2'
        ])->join(" ");

        $itemCount = count($items);

        $actionsClass = [];
        if ($itemCount > 5) {
            $actionsClass[] = "grid-cols-1";
        } else if ($itemCount > 3) {
            $actionsClass[] = "xl:grid-cols-1 lg:grid-cols-1 xl2:grid-cols-2";
        } else {
            $actionsClass[] = "grid-cols-2";
        }

        $actionsClass = collect($actionsClass)->join(" ");


        return Flex::make()
            ->className('space-x-5 w-full')
            ->items([
                GridSchema::make()->className("grid-filter-form flex-1 grid $cols gap-3 pr-1 w-full " . $this->addColumnsClass)->columns($items),
                Flex::make()
                    ->items([
                        GridSchema::make()->className("grid gap-y-3 $actionsClass")->columns($this->renderActions())
                    ]),
            ]);
    }

    /**
     * 禁用重置按钮
     */
    public function disableReset(bool $isDisableReset = true): Filter
    {
        $this->isDisableReset = $isDisableReset;
        return $this;
    }

    public function renderActions(): array
    {
        $actions = [
            Button::make()->label("查询")->type("submit")->level("primary")->icon('fa fa-search'),
        ];
        if (!$this->isDisableReset) {
            $actions[] = Button::make()->label("重置")->type("reset")->icon('fa fa-undo');
        }
        return $actions;

    }

    /**
     * 设置搜索表单默认值
     * @param array $defaultValue
     * @return Filter
     */
    public function defaultValue(array $defaultValue): Filter
    {
        foreach ($defaultValue as $key => $value) {
            $this->defaultValue["search"][$key] = $value;
        }
        return $this;
    }


    /**
     * @return array
     */
    public function getDefaultValue(): array
    {
        return $this->defaultValue;
    }

    public function where($name, $label = '', Closure $fun = null): Item
    {
        $this->addField($name, 'where', $fun);
        return $this->addItem($name, $label);
    }

    public function eq($name, $label = ''): Item
    {
        $this->addField($name, 'eq');
        return $this->addItem($name, $label);
    }

    public function like($name, $label): Item
    {
        $this->addField($name, 'like');
        return $this->addItem($name, $label);
    }

    public function between($name, $label): Item
    {
        $this->addField($name, 'between');
        return $this->addItem($name, $label);
    }

    public function in($name, $label): Item
    {
        $this->addField($name, 'in');
        return $this->addItem($name, $label);
    }

    public function notIn($name, $label): Item
    {
        $this->addField($name, 'notIn');
        return $this->addItem($name, $label);
    }
}