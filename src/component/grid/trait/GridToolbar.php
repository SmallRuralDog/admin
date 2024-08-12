<?php

namespace smallruraldog\admin\component\grid\trait;

use Closure;
use smallruraldog\admin\component\grid\Toolbar;


trait GridToolbar
{
    protected Toolbar $toolbar;

    /**
     * 禁用新增操作
     */
    public function disableCreate($bool = true): self
    {
        $this->toolbar->disableCreate($bool);
        return $this;
    }

    /**
     * 工具栏设置
     */
    public function toolbar(Closure $fun): self
    {
        $fun($this->toolbar);
        return $this;
    }
}