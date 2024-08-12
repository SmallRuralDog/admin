<?php

namespace smallruraldog\admin;

use smallruraldog\admin\model\SystemDept;
use smallruraldog\admin\model\SystemUser;
use Webman\Context;
use Webman\Http\Request;

class Admin
{


    /**
     * 获取当前登录用户ID
     * @return int
     */
    public static function userId(): int
    {
        return (int)request()->session()->get('admin_id');
    }

    /**
     * 获取当前登录用户信息
     * @return SystemUser|null
     */
    public static function userInfo(): ?SystemUser
    {
        $info = Context::get('admin_user');
        if (!$info) {
            $id = self::userId();
            if ($id) {
                $info = SystemUser::query()->find($id);
                Context::set('admin_user', $info);
            }
        }
        return $info;
    }


    /**
     * 获取部门的所有子部门ID
     */
    public static function getDeptSonById(?int $deptId, bool $addSelf = false): array
    {
        $dept = SystemDept::query()->find($deptId);
        if (!$dept) {
            return [];
        }
        $list = $dept->getAllChildren();
        $col = collect($list)->pluck('id');
        if ($addSelf) {
            $col->prepend($dept->getKey());
        }
        return $col->toArray();
    }

    /**
     * 获取部门和下级部门的用户ID
     */
    public static function getDeptUserIds(int $deptId, bool $addSelf = false): array
    {
        $deptIds = self::getDeptSonById($deptId, $addSelf);
        $userIds = SystemUser::query()->whereIn('dept_id', $deptIds)->pluck('id');
        return $userIds->toArray();
    }

    /**
     * 检查权限
     */
    public static function checkRoutePermission(Request $request): void
    {


        $user = Admin::userInfo();
        abort_if(!$user, 401, '请登录');
        $route = $request->route;
        $name = $route->getName();
        if (!$name) {
            $name = $route->getPath();
        }
        $hasPermission = $user->can($name);
        abort_if(!$hasPermission, 400, '没有权限');
    }
}