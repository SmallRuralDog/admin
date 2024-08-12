<?php

namespace smallruraldog\admin\component;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use smallruraldog\admin\component\form\FormHooks;
use smallruraldog\admin\component\form\FormMain;
use smallruraldog\admin\component\form\FormResource;
use smallruraldog\admin\renderer\form\AmisForm;
use smallruraldog\admin\renderer\Page;
use stdClass;
use support\Request;

class Form extends stdClass
{
    use ModelBase, FormMain, FormResource, FormHooks;

    public const REMOVE_FLAG_NAME = '_remove_flag';

    /**
     * 页面Page对象
     * @var Page
     */
    protected Page $page;

    /**路由名称 */
    protected string $routeName;

    protected Builder $builder;
    protected Model $model;


    protected Request $request;

    protected bool $isDialog = true;

    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->request = request();
        $routeName = request()->route->getName();
        $routeName = explode('.', $routeName)[0];
        $this->routeName = $routeName;
        $this->page = Page::make();
        $this->form = AmisForm::make();
    }

    /**
     * 创建一个表单
     * @param Builder $builder
     * @param Closure $fun
     * @return static
     */
    public static function make(Builder $builder, Closure $fun): static
    {
        $form = new static();
        $form->builder = $builder;
        $form->model = $builder->getModel();
        $fun($form);
        return $form;
    }

    /**
     * 获取模型
     * @return Model
     */
    public function model(): Model
    {
        return $this->model;
    }


    /**
     * 是否为弹窗
     */
    public function isDialog(): bool
    {
        return $this->isDialog;
    }

    /**
     * 获取AmisPage实例
     */
    public function usePage(): Page
    {
        return $this->page;
    }


    public function dialog(): Form
    {
        $this->isDialog = true;
        return $this;
    }


    public function render()
    {
        if ($this->isDialog()) {
            return $this->renderForm();
        }

        return $this->page;
    }
}