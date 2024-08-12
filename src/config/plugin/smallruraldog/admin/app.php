<?php
return [
    'enable' => true,

    'title' => getenv('ADMIN_TITLE') ?: "Webman Admin",//后台标题
    'route_suffix' => getenv('ADMIN_ROUTE_SUFFIX') ?: "admin",//后台路由前缀
    'domain' => getenv('ADMIN_DOMAIN'),//后台域名
];