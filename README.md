# webman admin

创建 webman 项目

```shell
composer create-project workerman/webman project-name
```

安装扩展

```shell
composer require smallruraldog/admin
```

更新扩展

```shell
composer update smallruraldog/admin
```

创建.env 文件

```shell
ADMIN_ROUTE_SUFFIX=admin
ADMIN_DOMAIN=

DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=webman-admin
DB_USERNAME=root
DB_PASSWORD=
DB_SOCKET=
```

配置 database.php

```php
return [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [
            'driver' => 'mysql',
            'host' => getenv('DB_HOST'),
            'port' => getenv('DB_PORT'),
            'database' => getenv('DB_DATABASE'),
            'username' => getenv('DB_USERNAME'),
            'password' => getenv('DB_PASSWORD'),
            'unix_socket' => getenv('DB_SOCKET'),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict'      => true,
            'engine'      => null,
        ],
    ],
];
```

初始化数据，只需要在首次安装时执行

```shell
php webman smallruraldog-admin:install
```

发布资源文件，如果有更新，需要重新发布

```shell
php webman smallruraldog-admin:assets
```

启动项目

```shell
php start.php start
```

访问后台

```
http://0.0.0.0:8787/admin
```

加入文件监听

修改配置文件 `config/process.php`
加入以下代码到 `monitorDir` 数组中
```php
base_path() . '/admin',
```

修改监进程文件 `process\Monitor.php`
注释掉以下两行代码
```php
if (DIRECTORY_SEPARATOR === '/' && isset($this->loadedFiles[$file->getRealPath()])) {
    //echo "$file updated but cannot be reloaded because only auto-loaded files support reload.\n";
    //continue;
}
```