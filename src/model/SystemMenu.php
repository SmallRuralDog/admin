<?php

namespace smallruraldog\admin\model;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use smallruraldog\admin\trait\HasDateTimeFormatter;


/**
 * @property int $id
 * @property string $type
 * @property int $parent_id
 * @property string $path
 * @property string $name
 * @property string $permission
 * @property string $icon
 * @property int $order
 * @property int $show
 * @property int $status
 * @property int $is_ext
 * @property int $ext_open_mode
 * @property string $active_menu
 * @property string $created_at
 * @property string $updated_at
 *
 *
 * @property-read Collection<int, SystemRole> $roles 用户角色
 */
class SystemMenu extends Model
{

    use HasDateTimeFormatter;

    protected $table = "system_menu";
    protected $guarded = [];


    const TYPE_DIR = 'dir';
    const TYPE_MENU = 'menu';
    const TYPE_PERMISSION = 'permission';

    const TYPE_LIST = [
        self::TYPE_DIR => '目录',
        self::TYPE_MENU => '菜单',
        self::TYPE_PERMISSION => '权限',
    ];

    const TYPE_LABEL = [
        self::TYPE_DIR => '<sapn class="label label-warning">目录</sapn>',
        self::TYPE_MENU => '<sapn class="label label-success">菜单</sapn>',
        self::TYPE_PERMISSION => '<sapn class="label label-danger">权限</sapn>',
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(SystemRole::class, 'system_role_menu', 'menu_id', 'role_id');
    }


    protected static function boot()
    {
        parent::boot();
        static::deleting(function (self $model) {
            $model->roles()->detach();
        });
    }
}