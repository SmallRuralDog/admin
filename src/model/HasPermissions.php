<?php

namespace smallruraldog\admin\model;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use smallruraldog\admin\Admin;
use smallruraldog\admin\enum\DataPermissionsType;
use Webman\Context;

trait HasPermissions
{

    /**
     * 是否是某个角色
     * @param string $role
     * @return bool
     */
    public function isRole(string $role): bool
    {
        return $this->roles->pluck('slug')->contains($role);
    }

    /**
     * 是否在某个角色中
     * @param array $roles
     * @return bool
     */
    public function inRoles(array $roles = []): bool
    {
        return $this->roles->pluck('slug')->intersect($roles)->isNotEmpty();
    }

    /**
     * 是否是管理员
     * @return bool
     */
    public function isAdministrator(): bool
    {
        return $this->isRole('administrator');
    }

    /**
     * 获取所有权限对象
     * @return Collection
     */
    public function getAllPermissionMenus()
    {
        if ($this->isAdministrator()) {
            return SystemMenu::query()
                ->orderByDesc('order')
                ->get();
        }
        $list = $this->roles?->pluck('menus')->flatten()->sortByDesc('order')->values();
        $deptList = $this->dept?->roles?->pluck('menus')->flatten()->sortByDesc('order')->values();
        return $list->merge($deptList);
    }

    /**
     * 获取所有权限ID
     * @return array
     */
    public function getAllPermissionIds(): array
    {
        $allMenus = $this->getAllPermissionMenus();
        return $allMenus->pluck('id')->toArray();
    }

    /**
     * 获取所有权限标识
     * @return array
     */
    public function getAllPermission(): array
    {

        $allMenus = $this->getAllPermissionMenus();
        $permission = $allMenus->pluck('permission')->filter(function ($item) {
            return !empty($item);
        })->toArray();
        $menuPaths = $allMenus->pluck('path')
            ->filter(function ($item) {
                return !empty($item);
            })
            ->map(function ($item) {
                return admin_url($item ?? "");
            })->unique()->toArray();
        return array_merge($permission, $menuPaths);
    }

    /**
     * 初始化权限，将权限存入上下文，方便后续使用，不用每次都查询数据库
     * @return void
     */
    public function initPermission(): void
    {
        $allPermission = $this->getAllPermission();
        Context::set('admin_permission', $allPermission);
    }

    /**
     * 权限检查
     * @param string $ability
     * @return bool
     */
    public function can(string $ability): bool
    {
        if (empty($ability)) {
            return true;
        }
        if ($this->isAdministrator()) {
            return true;
        }
        $permission = $this->getAllPermission();
        return collect($permission)->contains($ability);
    }

    /**
     * 没有某个权限
     * @param string $permission
     * @return bool
     */
    public function cannot(string $permission): bool
    {
        return !$this->can($permission);
    }

    /**
     * 获取用户数据权限类型
     * @return DataPermissionsType
     */
    public function getUserDataPermissionType(): DataPermissionsType
    {
        if ($this->isAdministrator()) {
            return DataPermissionsType::ALL;
        }

        $type = $this->roles?->pluck('data_permissions_type')->unique()->max() ?? '1';

        $deptType = $this->dept?->roles?->pluck('data_permissions_type')->unique()->max() ?? '1';

        if ($deptType > $type) {
            $type = $deptType;
        }

        return DataPermissionsType::tryFrom($type);

    }

    /**
     * 获取用户的部门ID和下级部门ID
     * @param bool $addSelf 是否包含自己
     * @return array<bool,array> [是否需要检查权限,部门ID数组]
     */
    public function getUserDeptSonIds(bool $addSelf = false): array
    {

        $check = false;
        $deptIds = [];
        $dept = $this->dept;
        switch ($this->getUserDataPermissionType()) {
            case DataPermissionsType::SELF:
                $check = true;
                $deptIds[] = $dept->getKey();
                break;
            case DataPermissionsType::DEPT:
                $check = true;
                $deptIds = [$dept->getKey()];
                break;
            case DataPermissionsType::DEPT_AND_SUB:
                $check = true;
                $deptIds = Admin::getDeptSonById($dept?->getKey(), $addSelf);
                break;
            case DataPermissionsType::ALL:
                break;
        }
        return [$check, $deptIds];
    }

    /**
     * 获取数据权限下可访问的用户ID
     * @param bool $addSelf 是否包含自己
     * @return array<bool,array> [是否需要检查,用户ID数组]
     */
    public function getDataPermissionUserIds(bool $addSelf = true): array
    {

        $check = false;
        $userIds = [];
        switch ($this->getUserDataPermissionType()) {
            case DataPermissionsType::SELF:
                $check = true;
                $userIds[] = $this->getKey();
                break;
            case DataPermissionsType::DEPT:
                $check = true;
                $userIds = SystemUser::query()->where('dept_id', $this->dept?->getKey())->pluck('id')->toArray();
                if ($addSelf) {
                    $userIds[] = $this->getKey();
                }
                break;
            case DataPermissionsType::DEPT_AND_SUB:
                $check = true;
                $userIds = SystemUser::query()->whereIn('dept_id', $this->getUserDeptSonIds(true))->pluck('id')->toArray();
                if ($addSelf) {
                    $userIds[] = $this->getKey();
                }
                break;
            case DataPermissionsType::ALL:
                break;
        }

        return [$check, $userIds];

    }

    /**
     * 获取菜单
     * @return array
     */
    public function getMenus(): array
    {
        if ($this->isAdministrator()) {
            $list = SystemMenu::query()
                ->whereIn('type', ['dir', 'menu'])
                ->orderByDesc('order')
                ->get();
        } else {
            $list = $this->roles?->pluck('menus')->flatten()->unique('id')->where(fn($item) => in_array($item->type, ['dir', 'menu']))->sortByDesc('order');
            $deptList = $this->dept?->roles?->pluck('menus')->flatten()->unique('id')->where(fn($item) => in_array($item->type, ['dir', 'menu']))->sortByDesc('order');
            $list = $list->merge($deptList);
        }

        $list = $list->map(function (SystemMenu $menu) {

            $res['id'] = (string)$menu->id;
            $res['path'] = Str::start($menu->path ?? "", '/');
            $res['name'] = $menu->name;
            $res['icon'] = $menu->icon;
            $res['parent_id'] = $menu->parent_id;
            $res['is_ext'] = (bool)$menu->is_ext;
            $res['ext_open_mode'] = $menu->ext_open_mode;
            $res['active_menu'] = (string)$menu->active_menu;
            $res['show'] = (bool)$menu->show;
            $res['params'] = $menu->params ?? [];
            return $res;
        });

        return [
            'active_menus' => $list->pluck('active_menus', 'id')->toArray(),
            'menus' => arr2tree($list->toArray()),
        ];

    }
}