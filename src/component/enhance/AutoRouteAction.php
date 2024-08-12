<?php

namespace smallruraldog\admin\component\enhance;


trait AutoRouteAction
{
    /**
     * API 配置对象类型
     *
     * https://aisuda.bce.baidu.com/amis/zh-CN/docs/types/api#%E5%A4%8D%E6%9D%82%E9%85%8D%E7%BD%AE
     * @param string $actionName
     * @param array $params
     * @param array $data
     * @param string $method
     * @return array
     */
    protected function action(string $actionName, array $params = [], array $data = [], string $method = "post"): array
    {
        $class = $this::class;
        $class=base64_encode($class);
        $d = [
            'class' => $class,
            'action' => $actionName,
            'data' => $data,
        ];
        return [
            'method' => $method,
            "url" => urldecode(route('admin.handleAction', $params)),
            "data" => $d,
        ];
    }
}