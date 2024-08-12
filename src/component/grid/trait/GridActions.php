<?php

namespace smallruraldog\admin\component\grid\trait;



use smallruraldog\admin\component\grid\Actions;

trait GridActions
{
    protected Actions $actions;
    /**禁用操作*/
    protected bool $disableAction = false;

    /**
     * 禁用所有操作
     */
    public function disableAction(): self
    {
        $this->disableAction = true;
        return $this;
    }

    /**
     * 禁用删除
     */
    public function disableDelete(): self
    {
        $this->actions->disableDelete();
        return $this;
    }

    /**
     * 禁用编辑
     */
    public function disableEdit(): self
    {
        $this->actions->disableEdit();
        return $this;
    }

    /**
     * 禁用批量删除
     */
    public function disableBulkDelete(bool $bool = true): self
    {
        $this->toolbar->disableBulkDelete($bool);
        return $this;
    }

    /**
     * 行操作设置
     */
    public function actions($fun): self
    {
        $fun($this->actions);
        return $this;
    }

    public function renderAction(): array
    {
        return $this->actions->render();
    }
}