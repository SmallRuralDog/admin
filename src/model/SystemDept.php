<?php

namespace smallruraldog\admin\model;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use smallruraldog\admin\trait\HasDateTimeFormatter;

/**
 * @property int $id
 * @property int $parent_id
 * @property string $name
 * @property int $order
 *
 * @property-read Collection<int, SystemMenu> $menus 部门菜单
 * @property-read Collection<int, SystemRole> $roles 部门角色
 */
class SystemDept extends Model
{
    use HasDateTimeFormatter;

    protected $table = 'system_dept';

    protected $guarded = [];

    protected $casts = [

    ];

    public function menus(): BelongsToMany
    {
        return $this->belongsToMany(SystemMenu::class, 'system_tenant_menu', 'tenant_id', 'menu_id');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(SystemRole::class, 'system_role_dept', 'dept_id', 'role_id');
    }


    /**
     * 获取所有子部门
     * @return array
     */
    public function getAllChildren(): array
    {
        $list = SystemDept::query()->get()->toArray();
        $tree = arr2tree($list);
        $itemTree = findTreeChildren($tree, $this->getKey());
        return tree2arr($itemTree);
    }

}