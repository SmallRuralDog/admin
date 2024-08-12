<?php

namespace smallruraldog\admin\controller\system;


use smallruraldog\admin\component\custom\DeptComponent;
use smallruraldog\admin\component\Form;
use smallruraldog\admin\component\Grid;
use smallruraldog\admin\controller\AdminController;
use smallruraldog\admin\model\SystemDept;
use smallruraldog\admin\renderer\Each;
use smallruraldog\admin\renderer\form\InputNumber;
use smallruraldog\admin\renderer\Tpl;

class DeptController extends AdminController
{

    protected function grid()
    {
        return Grid::make(SystemDept::query(), function (Grid $grid) {
            $grid->builder()->orderByDesc('order');
            $grid
                ->hideFooter()
                ->loadDataOnce()
                ->toTree();
            $grid->useCRUD()
                ->expandConfig([
                    'expand' => 'all'
                ])
                ->columnsTogglable(false)
                ->perPage(100)
                ->keepItemSelectionOnPageChange(true);
            $grid->column('name', '名称');
            $grid->column('roles', '绑定角色')
                ->remark('绑定角色后，该部门下的用户将拥有该角色的权限')
                ->useTableColumn(Each::make()->items(Tpl::make()->tpl("<span class='label label-default m-r-sm'>\${name}</span>")));
            $grid->column('order', '排序')->inputNumber();
            $grid->column('updated_at', '更新时间');
        });

    }

    protected function form()
    {
        return Form::make(SystemDept::query(), function (Form $form) {
            $form->customLayout([
                $form->item('name', '部门名称')
                    ->required()
                    ->useFormItem(),

                $form->item('parent_id', '上级部门')->value(0)->useFormItem(DeptComponent::make()->deptSelect()),

                $form->item('roles', '绑定角色')
                    ->useFormItem(DeptComponent::make()->deptBindRoleSelect()),

                $form->item('order', '排序')
                    ->value(1)
                    ->intType()
                    ->useFormItem(InputNumber::make()),

            ]);
        });
    }
}