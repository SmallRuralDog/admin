<?php

namespace smallruraldog\admin\component;

use Closure;
use Illuminate\Database\Eloquent\Builder;

use smallruraldog\admin\Admin;
use smallruraldog\admin\component\grid\Actions;
use smallruraldog\admin\component\grid\Filter;
use smallruraldog\admin\component\grid\GridModel;
use smallruraldog\admin\component\grid\Toolbar;
use smallruraldog\admin\component\grid\trait\GridActions;
use smallruraldog\admin\component\grid\trait\GridCRUD;
use smallruraldog\admin\component\grid\trait\GridData;
use smallruraldog\admin\component\grid\trait\GridDialogForm;
use smallruraldog\admin\component\grid\trait\GridFilter;
use smallruraldog\admin\component\grid\trait\GridPermission;
use smallruraldog\admin\component\grid\trait\GridToolbar;
use smallruraldog\admin\component\grid\trait\GridTree;
use smallruraldog\admin\renderer\CRUD;
use smallruraldog\admin\renderer\Page;
use support\Request;

class Grid
{
    use GridCRUD, GridToolbar, GridData, GridTree, GridFilter, GridActions, GridDialogForm, ModelBase,GridPermission;

    /**
     * 页面Page对象
     * @var Page
     */
    protected Page $page;

    /**路由名称 */
    protected string $routeName;


    protected Builder $builder;
    protected GridModel $model;

    /** 请求动作 */
    protected string $_action;

    protected Request $request;





    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->request = request();
        $this->_action = (string)request()->input('_action');

        $routeName = request()->route->getName();
        $routeName = explode('.', $routeName)[0];
        $this->routeName = $routeName;
        $this->page = Page::make();
        $this->crud = CRUD::make();
        $this->filter = new Filter();
        $this->actions = new Actions($this);
        $this->toolbar = new Toolbar($this);

        $admin = Admin::userInfo();

        $this->hasEditPermission = $admin->can($routeName . '.edit') && $admin->can($routeName . '.update');
        $this->hasDeletePermission = $admin->can($routeName . '.destroy');
        $this->hasCreatePermission = $admin->can($routeName . '.create') && $admin->can($routeName . '.store');

        $this->initCRUD();

        $this->dialogForm();
    }

    /**
     * 创建一个Grid
     */
    public static function make(Builder $builder, Closure $fun): static
    {
        $grid = new static();

        $grid->builder = $builder;
        $grid->model = new GridModel($builder, $grid);

        $fun($grid);
        return $grid;
    }

    /**获取页面组件*/
    public function usePage(): Page
    {
        return $this->page;
    }

    /**
     * 获取请求
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * 渲染Grid
     */
    public function render()
    {

        //获取数据
        if ($this->_action === "getData") {
            return $this->buildData();
        }

        $this->page->toolbar($this->toolbar->renderToolbar())->body([
            $this->renderHeader(),
            $this->renderCRUD(),
            $this->renderFooter(),
        ]);

        return $this->page;
    }
}