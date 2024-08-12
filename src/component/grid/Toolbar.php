<?php

namespace smallruraldog\admin\component\grid;



use smallruraldog\admin\component\Grid;
use smallruraldog\admin\renderer\action\AjaxAction;
use smallruraldog\admin\renderer\action\ReloadAction;
use smallruraldog\admin\renderer\Button;

class Toolbar
{
    protected Grid $grid;


    protected bool $disableCreate = false;//禁用创建按钮

    protected array $prependToolbarList = [];//前置工具栏
    protected array $addToolbarList = [];//添加工具栏

    protected array $prependHeaderToolbarList = [];//前置头部工具栏
    protected array $addHeaderToolbarList = [];//添加头部工具栏

    private bool $disableBulkDelete = false;//禁用批量删除
    private array $addBulkActionList = [];//添加批量操作


    public function __construct(Grid $grid)
    {
        $this->grid = $grid;

    }

    /**
     * 构造新增按钮操作
     */
    private function buildCreateButton(): Button
    {
        $link = 'get:' . $this->grid->getCreateUrl(['_dialog' => true]);
        return $this->grid->renderDialogForm($link);
    }

    /**构建刷新按钮*/
    private function buildReloadButton(): Button
    {
        return ReloadAction::make()->target($this->grid->getCrudName())->icon('fa fa-refresh')->label("刷新");
    }

    /**禁用新增按钮*/
    public function disableCreate(bool $bool = true): void
    {
        $this->disableCreate = $bool;
    }

    /**
     * Toolbar 系统默认
     * @return array
     */
    private function initToolbar(): array
    {
        $res = [];


        return $res;
    }

    /**
     * 添加页面前置工具栏
     * @param $node
     * @return $this
     */
    public function prependToolbar($node): Toolbar
    {
        $this->prependToolbarList[] = $node;
        return $this;
    }

    /**
     * 添加页面后置工具栏
     * @param $node
     * @return $this
     */
    public function addToolbar($node): Toolbar
    {
        $this->addToolbarList[] = $node;
        return $this;
    }

    /**
     * 渲染页面工具栏
     * renderToolbar
     * @return array
     */
    public function renderToolbar(): array
    {
        $res = collect();
        foreach ($this->prependToolbarList as $node) {
            $res->prepend($node);
        }
        foreach ($this->initToolbar() as $node) {
            $res->add($node);
        }
        foreach ($this->addToolbarList as $node) {
            $res->add($node);
        }
        return $res->toArray();
    }

    /**
     * HeaderToolbar 系统默认
     * @return array
     */
    private function initHeaderToolbar(): array
    {
        $res = collect();
        $res->add("reload");
        if (!$this->disableCreate && $this->grid->hasCreatePermission()) $res->add($this->buildCreateButton());
        $res->add("bulkActions");
        $res->add("pagination");
        return $res->toArray();
    }

    /**
     * 添加表格工具栏 前置
     * @param $node
     * @return $this
     */
    public function prependHeaderToolbar($node): Toolbar
    {
        $this->prependHeaderToolbarList[] = $node;
        return $this;
    }

    /**
     * 添加表格工具栏 后置
     * @param $node
     * @return $this
     */
    public function addHeaderToolbar($node): Toolbar
    {
        $this->addHeaderToolbarList[] = $node;
        return $this;
    }

    /**
     * 渲染表格头部工具栏
     * @return array
     */
    public function renderHeaderToolbar(): array
    {
        $res = collect();
        foreach ($this->prependHeaderToolbarList as $node) {
            $res->prepend($node);
        }
        foreach ($this->initHeaderToolbar() as $node) {
            $res->add($node);
        }
        foreach ($this->addHeaderToolbarList as $node) {
            $res->add($node);
        }
        return $res->toArray();
    }

    /**
     * 初始化底部工具栏
     * @return array
     */
    private function initFooterToolbar(): array
    {
        $res = collect();
        $res->add("statistics");
        $res->add("switch-per-page");
        $res->add("pagination");
        return $res->toArray();
    }

    /**
     * 渲染表格尾部工具栏
     * @return array
     */
    public function renderFooterToolbar(): array
    {
        $res = collect();
        foreach ($this->initFooterToolbar() as $node) {
            $res->add($node);
        }
        return $res->toArray();
    }

    /**
     * 禁用批量删除
     * @param bool $bool
     * @return void
     */
    public function disableBulkDelete(bool $bool = true): void
    {
        $this->disableBulkDelete = $bool;
    }

    /**
     * 初始化批量操作
     * 批量操作系统默认
     * @return array
     */
    private function initBulkAction(): array
    {
        $res = collect();
        if (!$this->disableBulkDelete && $this->grid->hasDeletePermission()) {
            $api = $this->grid->getDestroyUrl($this->grid->getBulkSelectIds());
            $res->add(AjaxAction::make()
                ->label("批量删除")
                ->level("danger")
                ->api($api)
                ->icon("fa fa-trash")
                ->confirmText("确定要删除吗？"));
        }
        return $res->toArray();
    }

    /**
     * 添加批量操作
     * @param $node
     * @return void
     */
    public function addBulkAction($node): void
    {
        $this->addBulkActionList[] = $node;
    }

    /**
     * 渲染批量操作
     * @return array
     */
    public function renderBulkActions(): array
    {
        $res = collect();
        foreach ($this->initBulkAction() as $node) {
            $res->add($node);
        }
        foreach ($this->addBulkActionList as $node) {
            $res->add($node);
        }
        return $res->toArray();
    }
}