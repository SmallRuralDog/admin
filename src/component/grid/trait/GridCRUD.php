<?php

namespace smallruraldog\admin\component\grid\trait;



use smallruraldog\admin\component\grid\Column;
use smallruraldog\admin\renderer\CRUD;

trait GridCRUD
{
    private CRUD $crud;//CRUD组件
    private string $crudName = "crud";//CRUD组件的name
    protected array $columns = [];//CRUD组件的列
    protected array $headers = [];//CRUD组件的头部
    protected array $footers = [];//CRUD组件的底部

    private bool $loadDataOnce = false;//是否只加载一次数据


    protected bool $hideFooter = false;//是否隐藏底部


    protected function initCRUD(): void
    {
        $this->crud
            ->autoFillHeight(true)
            ->columnsTogglable(false);
    }

    /**
     * 获得AmisCRUD组件
     * @return CRUD
     */
    public function useCRUD(): CRUD
    {
        return $this->crud;
    }

    /**
     * 获取CRUD组件的name
     * @return string
     */
    public function getCrudName(): string
    {
        return $this->crudName;
    }

    /**
     * 设置CRUD组件的name
     * @param string $crudName
     */
    public function setCrudName(string $crudName): void
    {
        $this->crudName = $crudName;
    }

    /**
     * 添加列表项
     * @param string $name
     * @param string $label
     * @return Column
     */
    public function column(string $name, string $label = ''): Column
    {
        return $this->addColumn($name, $label);
    }

    protected function addColumn($name = '', $label = ''): Column
    {
        $column = new Column($name, $label);
        $this->columns[] = $column;
        return $column;
    }

    /**
     * 获取CRUD组件的列
     * @return array
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * 是否一次性加载
     * @return bool
     */
    public function isLoadDataOnce(): bool
    {
        return $this->loadDataOnce;
    }

    /**
     * 一次性加载
     */
    public function loadDataOnce(bool $loadDataOnce = true): self
    {
        $this->loadDataOnce = $loadDataOnce;
        return $this;
    }

    /**
     * 添加头部组建
     */
    public function header(BaseSchema $header): self
    {
        $this->headers[] = $header;
        return $this;
    }

    /**渲染头部*/
    protected function renderHeader(): array
    {
        $header = [];
        foreach ($this->headers as $item) {
            $header[] = $item;
        }
        return $header;
    }

    /**
     * 添加底部组建
     */
    public function footer(BaseSchema $footer): self
    {
        $this->footers[] = $footer;
        return $this;
    }

    /**渲染底部*/
    protected function renderFooter(): array
    {
        $footer = [];
        foreach ($this->footers as $item) {
            $footer[] = $item;
        }
        return $footer;
    }

    /**
     * 隐藏CRUD底部
     */
    public function hideFooter(bool $hideFooter = true): self
    {
        $this->hideFooter = $hideFooter;
        return $this;
    }

    protected function renderCRUD(): CRUD
    {
        $this->crud->name($this->crudName)->className('admin-crud');


        //数据来源API
        $api = $this->getIndexUrl(['_action' => 'getData']);
        $this->crud->api($api);
        //快速编辑后用来批量保存的 API
        $this->crud->quickSaveApi($this->getQuickUpdateUrl("quickSave"));
        $this->crud->quickSaveItemApi($this->getQuickUpdateUrl("quickSaveItem"));


        $columns = [];
        foreach ($this->columns as $column) {
            /**@var Column $column */
            $columns[] = $column->render();
        }

        //添加过滤器配置
        $renderFilter = $this->renderFilter();
        if (count($renderFilter->getFilterField()) > 0) {
            $this->crud->filter($this->renderFilter());
        }

        //添加行操作配置
        if (!$this->disableAction) {
            if ($this->actions->isHoverAction()) {
                $this->crud->itemActions($this->renderAction());
            } else {
                $columns[] = [
                    'type' => 'operation',
                    'label' => '操作',
                    'width' => $this->actions->getWidth(),
                    'buttons' => $this->renderAction(),
                ];
            }
        }


        //添加工具栏配置
        $footerToolbar = $this->toolbar->renderFooterToolbar();
        if (!$this->hideFooter && count($footerToolbar) > 0) {
            $this->crud->footerToolbar($footerToolbar);
        }
        $headerToolbar = $this->toolbar->renderHeaderToolbar();
        if (count($headerToolbar) > 0) {
            $this->crud->headerToolbar($headerToolbar);
        }
        $bulkActions = $this->toolbar->renderBulkActions();
        if (count($bulkActions) > 0) {
            $this->crud->bulkActions($bulkActions);
        }

        //一次性加载
        if ($this->loadDataOnce) {
            $this->crud->loadDataOnce(true);
        }


        //添加列配置
        $this->crud->columns($columns);

        $this->crud->primaryField($this->getPrimaryKey());

        return $this->crud;
    }

}