<?php


namespace smallruraldog\admin\middleware;

use Exception;
use ReflectionClass;
use smallruraldog\admin\Admin;
use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

class AuthCheck implements MiddlewareInterface
{

    public function process(Request $request, callable $handler): Response
    {
        try {
            $controller = new ReflectionClass($request->controller);
            $noNeedLogin = $controller->getDefaultProperties()['noNeedLogin'] ?? [];
            if (!in_array($request->action, $noNeedLogin)) {
                if (Admin::userId() > 0) {
                    $user = Admin::userInfo();
                    $user->initPermission();
                    return $handler($request);
                }else{
                    throw new Exception('请登录后再操作');
                }
            }
            return $handler($request);
        } catch (Exception $exception) {
            if ($request->isAjax() || $request->acceptJson()) {
                return jsonError($exception->getMessage(), 401);
            }
            return redirect(admin_url('view/login'));
        }
    }
}