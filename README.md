# webman admin

创建webman项目
```shell
composer create-project workerman/webman project-name
```

安装扩展
```shell
composer require smallruraldog/admin
```

创建.env文件
```shell
ADMIN_ROUTE_SUFFIX=admin
ADMIN_DOMAIN=false

DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=webman-admin
DB_USERNAME=root
DB_PASSWORD=
DB_SOCKET=
```

配置database.php
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
````
 
初始化数据
```shell
php webman smallruraldog-admin:install
```

发布资源文件
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