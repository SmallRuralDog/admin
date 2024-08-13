<?php

namespace smallruraldog\admin\controller;

use Exception;
use smallruraldog\admin\Admin;
use support\Request;
use support\Response;

class IndexController extends AdminBase
{

    public array $noNeedLogin = ['root', 'index'];

    public array $noNeedPermission = ['root', 'index', 'userMenus'];


    public function root(Request $request)
    {

        $config = [
            'title' => admin_config('title'),
            'logo' => admin_config('logo'),
            'loginBanner' => admin_config('loginBanner'),
            'loginTitle' => admin_config('loginTitle'),
            'loginDesc' => admin_config('loginDesc'),
            'apiBase' => admin_url("/"),
            'prefix' => admin_config('route_suffix'),
            'captchaUrl' => route('admin.auth.captcha')
        ];

        $vite_assets = vite_assets();

        $configJson = json_encode($config);

        $title = htmlspecialchars($config['title']);
        $theme = $request->cookie('arco-theme');


        $amisVersion = '6.7.0';

        $css = '';
        if($theme=='dark'){
            $css = ' <link rel="stylesheet" href="/admin/amis/@'.$amisVersion.'/dark.css"/>';
        }



        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>$title</title>
    <link rel="stylesheet" href="/admin/amis/@$amisVersion/sdk.css"/>
    $css
    <link rel="stylesheet" href="/admin/amis/@$amisVersion/helper.css"/>
    <link rel="stylesheet" href="/admin/amis/@$amisVersion/iconfont.css"/>
    <script type="text/javascript" src="/admin/amis/@$amisVersion/sdk.js"></script>
    <script>
        window.AmisAdmin = $configJson
    </script>
</head>
<body>
<div id="app"></div>
$vite_assets
</body>
</html>
HTML;

    }

    public function userMenus(Request $request): Response
    {
        try {
            $menus = Admin::userInfo()->getMenus();
            return jsonData($menus);
        } catch (Exception $exception) {
            return jsonError($exception->getMessage());
        }

    }

    public function index(): Response
    {
        return redirect(admin_url("view/home"));
    }

}
