<?php
namespace smallruraldog\admin\middleware;


use Exception;
use ReflectionClass;
use smallruraldog\admin\Admin;
use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

class PermissionCheck implements MiddlewareInterface
{

    public function process(Request $request, callable $handler): Response
    {
        try {
            $controller = new ReflectionClass($request->controller);
            $noNeedLogin = $controller->getDefaultProperties()['noNeedLogin'] ?? [];
            $noNeedPermission = $controller->getDefaultProperties()['noNeedPermission'] ?? [];
            $noNeedAction = array_merge($noNeedLogin, $noNeedPermission);
            if (!in_array($request->action, $noNeedAction)) {
                Admin::checkRoutePermission($request);
            }
            return $handler($request);
        } catch (Exception $exception) {
            return amisError($exception->getMessage(), 403);
        }
    }


}