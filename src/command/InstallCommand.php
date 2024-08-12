<?php

namespace smallruraldog\admin\command;

use smallruraldog\admin\model\SystemMenu;
use smallruraldog\admin\model\SystemRole;
use smallruraldog\admin\model\SystemUser;
use support\Db;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InstallCommand extends Command
{
    protected static string $defaultName = 'smallruraldog-admin:install';
    protected static string $defaultDescription = '执行安装命令';


    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        //检测system_user表是否存在
        if (Db::table('system_user')->exists()) {
            $output->writeln('已安装');
            return self::SUCCESS;
        }


        $output->writeln('创建数据库表');
        Db::statement(<<<SQL
CREATE TABLE `system_dept` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '部门名称',
  `parent_id` int DEFAULT 0 COMMENT '父级ID',
  `order` int DEFAULT 0 COMMENT '排序',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
        $output->writeln('创建数据库表system_dept成功');

        Db::statement(<<<SQL
CREATE TABLE `system_menu` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('dir','menu','permission') COLLATE utf8mb4_unicode_ci DEFAULT 'dir' COMMENT '菜单类型',
  `parent_id` int DEFAULT 0 COMMENT '父级ID',
  `path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '路径',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '菜单名称',
  `permission` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '权限标识',
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '图标',
  `order` int DEFAULT NULL COMMENT '排序',
  `show` tinyint(1) DEFAULT 1 COMMENT '是否显示',
  `status` tinyint(1) DEFAULT 1 COMMENT '状态',
  `is_ext` tinyint(1) DEFAULT 0 COMMENT '是否外链',
  `ext_open_mode` int DEFAULT 1 COMMENT '外链打开方式',
  `active_menu` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '激活菜单',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  KEY `type` (`type`),
  KEY `path` (`path`),
  KEY `permission` (`permission`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
        $output->writeln('创建数据库表system_menu成功');

        Db::statement(<<<SQL
CREATE TABLE `system_role` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '角色名称',
  `slug` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '角色标识',
  `data_permissions_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '数据权限类型',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
        $output->writeln('创建数据库表system_role成功');

        Db::statement(<<<SQL
CREATE TABLE `system_user` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '用户名',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '密码',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '姓名',
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '头像',
  `dept_id` int DEFAULT 0 COMMENT '部门ID',
  `create_user_id` int DEFAULT 0 COMMENT '创建人ID',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `dept_id` (`dept_id`),
  KEY `create_user_id` (`create_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        $output->writeln('创建数据库表system_user成功');

        Db::statement(<<<SQL
CREATE TABLE `system_role_dept` (
  `role_id` int DEFAULT NULL COMMENT '角色ID',
  `dept_id` int DEFAULT NULL COMMENT '部门ID',
  UNIQUE KEY `role_id` (`role_id`,`dept_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        Db::statement(<<<SQL
CREATE TABLE `system_role_menu` (
  `role_id` int DEFAULT NULL COMMENT '角色ID',
  `menu_id` int DEFAULT NULL COMMENT '菜单ID',
  UNIQUE KEY `role_id` (`role_id`,`menu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
        Db::statement(<<<SQL
CREATE TABLE `system_role_user` (
  `role_id` int DEFAULT NULL COMMENT '角色ID',
  `user_id` int DEFAULT NULL COMMENT '用户ID',
  UNIQUE KEY `role_id` (`role_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );


        $output->writeln('创建数据库表成功');

        $this->initAdminDir();

        $this->initData();

        return self::SUCCESS;
    }

    protected static $pathRelation = array(
        __DIR__ . '/../command/sub/admin' => 'admin',
    );

    private function initAdminDir(): void
    {
        foreach (static::$pathRelation as $source => $dest) {
            if ($pos = strrpos($dest, '/')) {
                $parent_dir = base_path() . '/' . substr($dest, 0, $pos);
                if (!is_dir($parent_dir)) {
                    mkdir($parent_dir, 0777, true);
                    copy_dir($source, base_path() . "/$dest");
                }
            }
        }
    }


    private function initData(): void
    {

        $administrator = SystemUser::query()->create([
            'username' => 'admin',
            'password' => password_hash('admin', PASSWORD_DEFAULT),
            'name' => '超级管理员',
            'dept_id' => 0,
            'create_user_id' => 0,
        ]);
        $administratorRole = SystemRole::query()->create([
            'name' => '超级管理员',
            'slug' => 'administrator',
        ]);
        $administrator->roles()->attach($administratorRole->getKey());

        $menus = [
            [
                'type' => 'dir',
                'name' => '仪表盘',
                'icon' => 'fa-solid fa-gauge',
                'order' => 99,
                'children' => [
                    [
                        'type' => 'menu',
                        'name' => '数据概览',
                        'path' => 'home',
                        'order' => 1,
                    ]
                ]
            ],
            [
                'type' => 'dir',
                'name' => '系统管理',
                'icon' => 'fa-solid fa-gear',
                'order' => 98,
                'children' => [
                    [
                        'type' => 'menu',
                        'name' => '用户管理',
                        'path' => 'system/user',
                        'order' => 96,
                        'resource' => true,
                        'children' => [
                            [
                                'type' => 'menu',
                                'name' => '个人设置',
                                'order' => 1,
                                'path' => 'userSetting',
                                'show' => false,
                            ]
                        ]
                    ],
                    [
                        'type' => 'menu',
                        'name' => '角色管理',
                        'path' => 'system/role',
                        'order' => 97,
                        'resource' => true,

                    ],
                    [
                        'type' => 'menu',
                        'name' => '部门管理',
                        'path' => 'system/dept',
                        'order' => 99,
                        'resource' => true,
                    ],
                    [
                        'type' => 'menu',
                        'name' => '菜单管理',
                        'path' => 'system/menu',
                        'order' => 98,
                        'resource' => true,
                    ],
                ]
            ]
        ];

        foreach ($menus as $menu) {

            $menuITem = SystemMenu::query()->create([
                'type' => $menu['type'],
                'name' => $menu['name'],
                'icon' => $menu['icon'],
                'order' => $menu['order'],
                'path' => $menu['path'] ?? '',
            ]);
            if (isset($menu['children'])) {

                foreach ($menu['children'] as $child) {

                    $childItem = SystemMenu::query()->create([
                        'type' => $child['type'],
                        'name' => $child['name'],
                        'order' => $child['order'],
                        'parent_id' => $menuITem->getKey(),
                        'path' => $child['path'] ?? '',
                    ]);
                    if (isset($child['resource'])) {
                        $resourceArr = ['.index', '.create', '.edit', '.destroy', '.store', '.update'];
                        foreach ($resourceArr as $r) {
                            $permissionName = getNameByResourceRoute($r);
                            SystemMenu::query()->create([
                                'type' => 'permission',
                                'name' => $permissionName,
                                'order' => 1,
                                'parent_id' => $childItem->getKey(),
                                'permission' => $child['path'] . $r,
                            ]);
                        }
                    }
                    if (isset($child['children'])) {
                        foreach ($child['children'] as $subChild) {
                            SystemMenu::query()->create([
                                'type' => $subChild['type'],
                                'name' => $subChild['name'],
                                'order' => $subChild['order'],
                                'parent_id' => $childItem->getKey(),
                                'path' => $subChild['path'] ?? '',
                                'show' => $subChild['show'] ?? true,
                                'active_menu' => (string)$childItem->getKey(),
                            ]);
                        }
                    }

                }

            }

        }

    }

}