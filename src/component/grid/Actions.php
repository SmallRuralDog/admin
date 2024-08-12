<?php

namespace smallruraldog\admin\component\grid;

use Closure;
use smallruraldog\admin\component\Grid;
use smallruraldog\admin\renderer\action\AjaxAction;
use smallruraldog\admin\renderer\action\DialogAction;
use smallruraldog\admin\renderer\Button;
use smallruraldog\admin\renderer\DropdownButton;


class Actions
{

    protected Grid $grid;

    protected bool $disableDeleteAction = false;
    protected bool $disableEditAction = false;

    protected bool $hoverAction = false;
    protected bool $rowAction = true;

    protected array $prependList = [];
    protected array $addList = [];

    protected float|string $width = 150;

    protected ?Closure $callDeleteActionFun = null;
    protected ?Closure $callEditActionFun = null;

    public function __construct(Grid $grid)
    {
        $this->grid = $grid;
    }


    /**构建删除操作*/
    private function buildDeleteAction(): Button
    {
        $keyName = $this->grid->getPrimaryKey();
        $api = $this->grid->getDestroyUrl('${' . $keyName . '}');
        return AjaxAction::make()
            ->label("删除")
            ->level("link")
            ->confirmText("确定要删除吗？")
            ->confirmTitle("操作确认")
            ->api($api)
            ->className('text-danger')
            ->icon('fa fa-trash-can mr-2');
    }

    /**
     * 自定义删除操作
     */
    public function callDeleteAction(Closure $closure): void
    {
        $this->callDeleteActionFun = $closure;
    }

    private function buildEditAction(): DialogAction
    {
        $keyName = $this->grid->getPrimaryKey();

        $api = 'get:' . $this->grid->getEditUrl($keyName) . '?_dialog=true';
        return $this->grid->renderDialogForm($api, true);

    }

    /**
     * 自定义编辑操作
     */
    public function callEditAction(Closure $closure): void
    {
        $this->callEditActionFun = $closure;
    }

    /**
     * 使用鼠标悬浮显示操作栏
     */
    public function hoverAction(bool $hoverAction = true): self
    {
        $this->hoverAction = $hoverAction;
        $this->rowAction = true;
        return $this;
    }

    /**是否使用鼠标悬浮显示操作栏*/
    public function isHoverAction(): bool
    {
        return $this->hoverAction;
    }

    /**
     * 使用行内操作栏
     */
    public function rowAction(bool $rowAction = true): Actions
    {
        $this->rowAction = $rowAction;
        return $this;
    }

    /**是否使用行内操作栏*/
    public function isRowAction(): bool
    {
        return $this->rowAction;
    }

    /**
     * 设置操作栏宽度，悬浮显示操作栏不生效
     */
    public function width(float|int|string $width): Actions
    {
        $this->width = $width;
        return $this;
    }

    /**获取操作栏宽度*/
    public function getWidth(): float|int|string
    {
        return $this->width;
    }

    /**
     * 禁用删除按钮
     */
    public function disableDelete(): void
    {
        $this->disableDeleteAction = true;
    }

    /**
     * 禁用编辑按钮
     */
    public function disableEdit(): void
    {
        $this->disableEditAction = true;
    }


    /**初始化操作*/
    private function initAction(): array
    {
        $actions = collect([]);


        if (!$this->disableEditAction && $this->grid->hasEditPermission()) {
            $editAction = $this->buildEditAction();
            if ($this->callEditActionFun) {
                call_user_func($this->callEditActionFun, $editAction);
            }
            $actions->add($editAction);
        }
        if (!$this->disableDeleteAction && $this->grid->hasDeletePermission()) {
            $deleteAction = $this->buildDeleteAction();
            if ($this->callDeleteActionFun) {
                call_user_func($this->callDeleteActionFun, $deleteAction);
            }
            $actions->add($deleteAction);
        }

        return $actions->toArray();
    }

    /**
     * 前置添加操作
     */
    public function prepend($node): self
    {
        $this->prependList[] = $node;
        return $this;
    }

    /**
     * 后置添加操作
     */
    public function add($node): self
    {
        $this->addList[] = $node;
        return $this;
    }

    /**
     * 渲染操作
     * @return array
     */
    public function render(): array
    {
        $res = collect();
        foreach ($this->prependList as $node) {
            $res->prepend($node);
        }
        foreach ($this->initAction() as $node) {
            $res->add($node);
        }
        foreach ($this->addList as $node) {
            $res->add($node);
        }
        if ($this->isRowAction()) {
            return $res->toArray();
        }
        return [DropdownButton::make()->label("操作")->level('link')->buttons($res->toArray())];
    }
}