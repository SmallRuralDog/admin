<?php

namespace smallruraldog\admin\component\grid\trait;

use Closure;

trait GridData
{
    public ?Closure $callRows = null;
    public ?Closure $callRow = null;

    /**
     * 处理数据集合
     */
    public function useRows(Closure $closure): self
    {
        $this->callRows = $closure;
        return $this;
    }

    /**
     * 处理每一条数据
     */
    public function useRow(Closure $closure): self
    {
        $this->callRow = $closure;
        return $this;
    }

    /**
     * 构建数据
     * @return array
     */
    protected function buildData(): array
    {
        return $this->model->buildData();
    }
}