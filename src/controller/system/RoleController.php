<?php

namespace smallruraldog\admin\controller\system;



use smallruraldog\admin\Admin;
use smallruraldog\admin\component\Form;
use smallruraldog\admin\component\Grid;
use smallruraldog\admin\controller\AdminController;
use smallruraldog\admin\enum\DataPermissionsType;
use smallruraldog\admin\model\SystemRole;
use smallruraldog\admin\renderer\Button;
use smallruraldog\admin\renderer\form\Group;
use smallruraldog\admin\renderer\form\InputTree;
use smallruraldog\admin\renderer\form\Select;

class RoleController extends AdminController
{

    protected function grid()
    {
        return Grid::make(SystemRole::query(), function (Grid $grid) {


            $grid->column('name', '名称');
            $grid->column('slug', '标识');
            $grid->column('data_permissions_type', '数据权限类型')->mapping(SystemRole::DATA_PERMISSIONS_TYPE);
            $grid->column('created_at', '创建时间');
            $grid->setDialogFormSize('lg');

            $grid->actions(function (Grid\Actions $actions) {

                $actions->callEditAction(function (Button $button) {
                    $button->visibleOn('slug != "administrator"');
                });

                $actions->callDeleteAction(function (Button $button) {
                    $button->visibleOn('slug != "administrator"');
                });

            });

        });
    }

    protected function form()
    {
        return Form::make(SystemRole::query(), function (Form $form) {
            $form->customLayout([
                Group::make()->label("基础信息")->body(
                    [
                        $form->item('name', '名称')->required()->useFormItem(),
                        $form->item('slug', '标识')->required()->useFormItem(),
                    ]
                ),

                $form->item('data_permissions_type', '数据权限类型')
                    ->required()
                    ->value(DataPermissionsType::SELF->getValue())
                    ->useFormItem(Select::make()
                        ->options(SystemRole::DATA_PERMISSIONS_TYPE)
                    ),


                $form->item('menus', '菜单与权限')->useFormItem(InputTree::make()
                    ->extractValue(true)
                    ->joinValues(false)
                    ->labelField("name")
                    ->valueField("id")
                    ->multiple(true)
                    ->autoCheckChildren(true)
                    ->cascade(true)
                    ->searchable(true)
                    ->showOutline(true)
                    ->initiallyOpen(false)
                    ->unfoldedLevel(2)
                    ->treeContainerClassName("role-menu-tree")
                    ->options(function () {
                        $list = Admin::userInfo()->getAllPermissionMenus()->toArray();
                        return arr2tree($list);
                    })),
            ]);
        });

    }

}