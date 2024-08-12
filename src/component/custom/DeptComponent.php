<?php

namespace smallruraldog\admin\component\custom;


use smallruraldog\admin\Admin;
use smallruraldog\admin\component\enhance\AutoRoute;
use smallruraldog\admin\enum\DataPermissionsType;
use smallruraldog\admin\model\SystemDept;
use smallruraldog\admin\model\SystemRole;
use smallruraldog\admin\renderer\form\Select;
use smallruraldog\admin\renderer\form\TreeSelect;
use support\Request;

class DeptComponent extends AutoRoute
{

    public function deptBindRoleSelect()
    {

        return Select::make()
            ->extractValue(true)
            ->joinValues(false)
            ->multiple(true)
            ->labelField("name")
            ->valueField("id")
            ->searchable(true)
            ->options(function () {
                return SystemRole::query()->get(['id', 'name', 'slug'])->toArray();
            });
    }


    public function deptSelect()
    {

        $options = $this->_deptSelect();
        return TreeSelect::make()
            ->labelField('name')
            ->valueField("id")
            ->options($options)
            ->showIcon(false)
            ->withChildren(true)
            ->rootCreatable(true);
    }


    public function _deptSelect()
    {
        $admin = Admin::userInfo();

        $orm = SystemDept::query();
        $list = match ($admin->getUserDataPermissionType()) {
            DataPermissionsType::SELF => [],
            DataPermissionsType::DEPT => $orm->where('id', $admin->dept?->getKey())->get()->sortByDesc('order')->toArray(),
            DataPermissionsType::DEPT_AND_SUB => $orm->whereIn('id', $admin->getUserDeptSonIds())->get()->sortByDesc('order')->toArray(),
            default => $orm->get()->sortByDesc('order')->toArray(),
        };

        return arr2tree($list);
    }

}