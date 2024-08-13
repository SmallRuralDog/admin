<?php

namespace smallruraldog\admin\controller\system;


use mysql_xdevapi\Exception;
use Respect\Validation\Validator;
use smallruraldog\admin\Admin;
use smallruraldog\admin\component\custom\DeptComponent;
use smallruraldog\admin\component\Form;
use smallruraldog\admin\component\Grid;
use smallruraldog\admin\controller\AdminController;
use smallruraldog\admin\model\SystemUser;
use smallruraldog\admin\renderer\Each;
use smallruraldog\admin\renderer\form\Group;
use smallruraldog\admin\renderer\form\InputDateRange;
use smallruraldog\admin\renderer\form\InputImage;
use smallruraldog\admin\renderer\form\InputText;
use smallruraldog\admin\renderer\Tpl;

class UserController extends AdminController
{
    protected function grid()
    {
        return Grid::make(SystemUser::query()->with(['createUser']), function (Grid $grid) {

            $admin = Admin::userInfo();

            if (!$admin->isAdministrator()) {
                [$check, $deptIds] = $admin->getUserDeptSonIds();
                if ($check) {
                    $grid->builder()->where('dept_id', $deptIds);
                }

            }


            $grid->column('username', '用户名');
            $grid->column('name', '姓名');
            $grid->column('roles', '角色')
                ->remark('绑定角色后，该用户将拥有该角色的权限')
                ->useTableColumn(Each::make()->items(Tpl::make()->tpl("<span class='label label-default m-l-sm'>\${name}</span>")));

            $grid->column('dept.name', '所属部门')->remark('用户所属部门,如果部门绑定了权限,用户将继承部门权限');

            $grid->column('create_user.name', '创建人');

            $grid->column('created_at', '创建时间');

            $grid->filter(function (Grid\Filter $filter) {

                $filter->setAddColumnsClass("m:grid-cols-1");

                $filter->like('username', '用户名');
                $filter->like('name', '姓名');


                $filter->where('dept_id', '部门', function ($q, $v) {
                    $ids = Admin::getDeptSonById((int)$v, true);
                    $q->whereIn('dept_id', $ids);
                })->useFormItem(DeptComponent::make()->deptSelect()->searchable(true));

                $filter->where('created_at', '创建时间', function ($q, $v) {
                    $v = explode(",", $v);
                    $q->where('created_at', '>=', $v[0]);
                    $q->where('created_at', '<=', $v[1]);
                })->useFormItem(InputDateRange::make());
            });

        });
    }

    protected function form()
    {
        return Form::make(SystemUser::query(), function (Form $form) {

            $form->customLayout([
                Group::make()->body([
                    $form->item('username', '用户名')->required()->useFormItem(),
                    $form->item('name', '昵称')
                        ->required()
                        ->useFormItem(),
                ]),
                Group::make()->body([

                    $form->item('password', '密码')
                        ->required($this->isCreate)
                        ->useFormItem(InputText::make()->password()),
                    $form->item('password_confirmation', '确认密码')
                        ->required($this->isCreate)
                        ->useFormItem(InputText::make()->password()),
                ]),
                Group::make()->body(function () use ($form) {
                    $body = [
                        $form->item('dept_id', '所属部门')
                            ->value(0)->useFormItem(DeptComponent::make()->deptSelect()),
                    ];
                    if (Admin::userInfo()->isAdministrator()) {
                        $body[] = $form->item('roles', "角色")
                            ->useFormItem(DeptComponent::make()->deptBindRoleSelect());
                    }
                    return $body;
                }),
            ]);

            $form->useValidatorEnd(function (Form $form, $data) {
                if (data_get($data, 'password')) {
                    Validator::equals(data_get($data, 'password_confirmation'))->setName('确认密码')->check($data['password']);
                }
            });


            $form->saving(function (Form $form) {
                $form->deleteInput('password_confirmation');
                if ($form->password && $form->model()->get('password') != $form->password) {
                    $form->password = password_hash($form->password, PASSWORD_DEFAULT);
                }
                if (!$form->password) {
                    $form->deleteInput('password');
                    $form->ignored('password');
                }
            });
        });

    }

}