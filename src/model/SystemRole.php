<?php

namespace smallruraldog\admin\model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use smallruraldog\admin\trait\HasDateTimeFormatter;


class SystemRole extends Model
{

    use HasDateTimeFormatter;

    protected $table = "system_role";

    protected $guarded = [];

    protected $casts = [

    ];

    public function administrators(): BelongsToMany
    {
        return $this->belongsToMany(SystemUser::class, 'system_role_user', 'role_id', 'user_id');
    }

    public function menus(): BelongsToMany
    {
        return $this->belongsToMany(SystemMenu::class, 'system_role_menu', 'role_id', 'menu_id');
    }

    public function depts()
    {
        return $this->belongsToMany(SystemDept::class, 'system_role_dept', 'role_id', 'dept_id');
    }


    protected static function boot()
    {
        parent::boot();
        static::deleting(function (SystemRole $model) {
            abort_if($model->slug == 'administrator', 400, '管理员角色不能删除');
            $model->administrators()->detach();
            $model->menus()->detach();
            $model->depts()->detach();
        });
    }


    /**
     * 数据权限类型
     */
    const DATA_PERMISSIONS_TYPE = [
        '1' => '本人',
        '2' => '本部门',
        '3' => '本部门及子部门',
        '99' => '全部',
    ];


}