<?php

namespace smallruraldog\admin\component\grid\trait;


use smallruraldog\admin\component\Grid;
use smallruraldog\admin\component\grid\Filter;

trait GridFilter
{
    private Filter $filter;

    /**
     * 查询过滤器
     * @param $fun
     * @return Grid
     */
    public function filter($fun): Grid
    {
        $this->crud->filterTogglable(false);
        $fun($this->filter);
        return $this;
    }

    public function getFilterField(): array
    {
        return $this->filter->getFilterField();
    }


    private function buildFilter(): void
    {
        $this->filter->body($this->filter->renderBody());
        $this->filter->data($this->filter->getDefaultValue());
    }

    public function renderFilter(): Filter
    {
        $this->buildFilter();
        return $this->filter;
    }
}