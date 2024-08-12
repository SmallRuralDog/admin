<?php


use smallruraldog\admin\controller\AuthController;
use smallruraldog\admin\controller\HandleController;
use smallruraldog\admin\controller\IndexController;
use smallruraldog\admin\controller\system\DeptController;
use smallruraldog\admin\controller\system\MenuController;
use smallruraldog\admin\controller\system\RoleController;
use smallruraldog\admin\controller\system\UserController;
use smallruraldog\admin\middleware\AuthCheck;
use smallruraldog\admin\middleware\DomainCheck;
use smallruraldog\admin\middleware\PermissionCheck;
use Webman\Route;

Route::disableDefaultRoute("admin");

$suffix = config('plugin.smallruraldog.admin.app.route_suffix');
Route::group('/' . $suffix, function () {

    Route::get('', [IndexController::class, 'index'])->name('admin.index');
    Route::get('/view', [IndexController::class, 'root']);
    Route::get('/view/{name}', [IndexController::class, 'root']);
    Route::get('/view/{group}/{name}', [IndexController::class, 'root']);
    Route::get('/userMenus', [IndexController::class, 'userMenus']);

    Route::any('/_handle_action_', [HandleController::class, 'action'])->name('admin.handleAction');

    Route::post('/auth/login', [AuthController::class, 'login'])->name('admin.auth.login');
    Route::post('/auth/logout', [AuthController::class, 'logout'])->name('admin.auth.logout');


    Route::resource("/system/dept", DeptController::class);
    Route::resource("/system/menu", MenuController::class);
    Route::resource("/system/role", RoleController::class);

    Route::resource("/system/user", UserController::class);


    Route::any("/userSetting", [AuthController::class, 'userSetting'])->name('admin.userSetting');

    // 加载admin应用下的路由配置
    require_once base_path('admin/route.php');
})->middleware([
    DomainCheck::class,
    AuthCheck::class,
    PermissionCheck::class,
]);