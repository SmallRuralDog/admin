<?php

namespace smallruraldog\admin\component\grid\trait;

use Closure;
use smallruraldog\admin\component\grid\DialogForm;
use smallruraldog\admin\renderer\action\DialogAction;

trait GridDialogForm
{
    protected bool $isDialogForm = true;

    protected DialogForm $dialogForm;

    /**
     * 弹窗表单模式
     * @return DialogForm
     */
    protected function dialogForm(): DialogForm
    {
        $this->isDialogForm = true;
        $this->dialogForm = new DialogForm($this);
        return $this->dialogForm;
    }

    /**
     * 设置弹窗表单大小 sm | lg | md | xl
     * @param string $size
     * @return void
     */
    public function setDialogFormSize(string $size): void
    {
        $this->dialogForm->size($size);
    }

    /**
     * 使用弹窗表单
     */
    public function useDialogForm(Closure $closure):self
    {
        $closure($this->dialogForm);
        return $this;
    }

    public function renderDialogForm($api, $edit = false): DialogAction
    {
        return $this->dialogForm->render($api, $edit);
    }

    public function isDialogForm(): bool
    {
        return $this->isDialogForm;
    }
}