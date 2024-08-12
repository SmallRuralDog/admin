<?php

namespace smallruraldog\admin\component\grid;



use smallruraldog\admin\component\Form;
use smallruraldog\admin\component\Grid;
use smallruraldog\admin\renderer\action\DialogAction;
use smallruraldog\admin\renderer\Service;

class DialogForm
{
    protected Grid $grid;
    protected ?Form $form = null;

    protected DialogAction $createDialogAction;
    protected DialogAction $editDialogAction;

    protected int|string $size = 'lg';

    public function __construct(Grid $grid)
    {
        $this->grid = $grid;
        $this->createDialogAction = DialogAction::make()->label("新建")->level('primary')->icon('fa fa-add');
        $this->editDialogAction = DialogAction::make()->label("编辑")->level('link')->icon('fa fa-edit icon-mr mr-2');
    }

    /**
     * 设置弹窗大小
     */
    public function size(int|string $size): DialogForm
    {
        $this->size = $size;
        return $this;
    }

    /**
     * 较小的弹框
     */
    public function sm(): void
    {
        $this->size("sm");
    }

    /**
     * 较大的弹框
     */
    public function lg(): void
    {
        $this->size("lg");
    }

    /**
     * 很大的弹框
     */
    public function xl(): void
    {
        $this->size("xl");
    }

    /**
     * 占满屏幕的弹框
     */
    public function full(): void
    {
        $this->size("full");
    }

    /**
     * 设置表单，添加操作时，不需要异步加载表单渲染配置
     */
    public function form(Form $form): DialogForm
    {
        $this->form = $form;
        $this->form->dialog();
        return $this;
    }

    /**
     * 渲染弹窗
     * @param $api
     * @param bool $edit
     * @return DialogAction
     */
    public function render($api, bool $edit = false): DialogAction
    {
        if ($edit) {
            $this->editDialogAction->dialog([
                'title' => '编辑',
                'size' => $this->size,
                'body' => Service::make()->schemaApi($api),
            ]);
            return $this->editDialogAction;
        }
        $this->createDialogAction->dialog([
            'title' => '新建',
            'size' => $this->size,
            'body' => $this->form ?: Service::make()->schemaApi($api),
        ]);
        return $this->createDialogAction;
    }

}