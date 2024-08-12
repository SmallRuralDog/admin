<?php

namespace smallruraldog\admin\component\custom;


use smallruraldog\admin\component\enhance\AutoRoute;
use smallruraldog\admin\model\SystemMenu;
use smallruraldog\admin\renderer\form\Checkboxes;
use smallruraldog\admin\renderer\form\InputText;
use smallruraldog\admin\renderer\form\TreeSelect;
use support\Request;
use Webman\Route;

class MenuComponent extends AutoRoute
{

    public function routeParentSelect()
    {
        return TreeSelect::make()
            ->labelField('name')
            ->valueField("id")
            ->source($this->action('_routeParentSelect', [
                'type' => '${type}'
            ]));
    }

    public function _routeParentSelect(Request $request)
    {
        $type = $request->input('type');

        $orm = SystemMenu::query();

        if ($type == "menu") {
            $orm->whereIn('type', ['dir', 'menu']);
        }
        if ($type == "permission") {
            $orm->whereIn('type', ['dir', 'menu']);
        }
        if ($type == "dir") {
            $list = [];
        } else {
            $list = $orm
                ->orderByDesc('order')
                ->get()->toArray();
        }

        return jsonData([
            ['id' => 0, 'name' => '根目录', 'children' => arr2tree($list)],
        ]);
    }

    public function routePathInput()
    {

        return InputText::make()->autoComplete($this->action('routePathInputAutoComplete', [
            'path' => '${path}'
        ]));
    }

    public function routePathInputAutoComplete(Request $request)
    {
        $path = $request->input('path');
        $routes = Route::getRoutes();


        $itemRoute = [];

        $suffix = admin_config('route_suffix');

        $nameList = ['.create', '.edit', '.destroy', '.store', '.update'];

        $hiddenList = ['view', '_handle_action_', 'auth/login', 'auth/logout'];

        $hiddenList = array_merge($hiddenList, SystemMenu::query()->pluck('path')->toArray());

        foreach ($routes as $route) {


            $isCheck = false;
            foreach ($nameList as $name) {
                $routeName = $route->getName();
                if ($routeName && str_ends_with($routeName, $name)) {
                    $isCheck = true;
                    break;
                }
            }
            if ($isCheck) {
                continue;
            }


            $routePath = $route->getPath();
            $routePath = str_replace('/' . $suffix, '', $routePath);
            if (str_starts_with($routePath, '/')) {
                $routePath = substr($routePath, 1);
            }
            if ($path && !str_starts_with($routePath, $path)) continue;
            //有参数的路由不显示
            if (str_contains($routePath, '{')) {
                continue;
            }
            if (in_array($routePath, $hiddenList)) {
                continue;
            }

            $itemRoute[] = $routePath;
        }
        //数组去重
        $itemRoute = array_unique($itemRoute);

        return jsonData($itemRoute);
    }

    public function routePermissionInput()
    {
        $api = $this->action('_routePermissionInput', [
            'path' => '${path}'
        ]);
        return Checkboxes::make()
            ->source($api)
            ->joinValues(false)
            ->extractValue(true)
            ->columnsCount([1, 3, 3, 3, 3, 3])
            ->checkAll(true);

    }

    public function _routePermissionInput(Request $request, array $data)
    {
        $path = $request->input('path');
        if (!$path) {
            return jsonData([]);
        }
        $itemRoute = [];
        $nameList = ['.index', '.create', '.edit', '.destroy', '.store', '.update'];
        foreach ($nameList as $name) {
            $routeName = $path . $name;
            if (route($routeName)) {
                $itemRoute[] = [
                    'label' => getNameByResourceRoute($routeName) . '-' . $routeName,
                    'value' => $routeName,
                ];
            }
        }
        return jsonData($itemRoute);
    }

}