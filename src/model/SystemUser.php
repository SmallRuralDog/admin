<?php

namespace smallruraldog\admin\model;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use smallruraldog\admin\Admin;
use smallruraldog\admin\trait\HasDateTimeFormatter;

/**
 * @property int $id
 * @property string $name
 * @property string $username
 * @property string $password
 * @property int $dept_id
 * @property int $create_user_id
 *
 * @property-read Collection<int, SystemRole> $roles 用户角色
 * @property-read SystemDept|null $dept 部门
 * @property-read SystemUser|null $createUser 创建人
 *
 */
class SystemUser extends Model
{
    use HasPermissions, HasDateTimeFormatter;

    protected $table = "system_user";

    protected $hidden = [
        'password',
    ];
    protected $guarded = [];

    protected $casts = [
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(SystemRole::class, 'system_role_user', 'user_id', 'role_id');
    }


    public function dept(): BelongsTo
    {
        return $this->belongsTo(SystemDept::class, 'dept_id', 'id');
    }

    public function createUser(): BelongsTo
    {
        return $this->belongsTo(SystemUser::class, 'create_user_id', 'id');
    }


    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (SystemUser $model) {
            $model->create_user_id = Admin::userId();
        });

        static::deleting(function (SystemUser $model) {
            abort_if($model->getKey() == Admin::userId(), 400, '不能删除自己');
            $model->roles()->detach();
        });
    }
}