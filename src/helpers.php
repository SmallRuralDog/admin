<?php

use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Shopwwi\WebmanFilesystem\Facade\Storage;
use support\Response;


function admin_config(string $key)
{
    return config('plugin.smallruraldog.admin.app.' . $key);
}

/**
 * Here is your custom functions.
 */

function admin_route(string $name, array $parameters = []): string
{
    return route($name, $parameters);
}

function admin_url(string $path): string
{
    $suffix = admin_config('route_suffix');
    if (str_ends_with($path, '/')) {
        $path = substr($path, 0, -1);
    }
    if (str_starts_with($path, '/')) {
        $path = substr($path, 1);
    }
    if (str_starts_with($path, "/")) {
        $path = substr($path, 1);
    }
    return "/$suffix/$path";
}


if (!function_exists('admin_file_url')) {
    function admin_file_url($path)
    {
        if (!$path) {
            return $path;
        }

        if (Str::startsWith($path, ["http://", "https://"])) {
            return $path;
        }
        return Storage::adapter(admin_config('upload.disk'))->url($path);
    }
}

if (!function_exists('admin_file_restore_path')) {
    function admin_file_restore_path($url)
    {
        if (!$url) {
            return $url;
        }
        if (Str::startsWith($url, ["http://", "https://"])) {
            $base_url = Storage::adapter(admin_config('upload.disk'))->url('');
            $url = str_replace($base_url, '', $url);
        }
        return $url;
    }
}

/**
 * 抛出异常
 */
function abort_if(bool $condition, int $code, string $message): void
{
    if ($condition) {
        throw new Exception($message, $code);
    }
}


/**
 * 成功返回
 * @param mixed $data
 * @param int $status
 * @return Response
 */
function jsonData(mixed $data, int $status = 0): Response
{
    return json([
        'status' => $status,
        'data' => $data,
    ]);
}

/**
 * 失败返回
 * @param string $message
 * @param int $status
 * @return Response
 */
function jsonError(string $message, int $status = 400): Response
{
    return new Response($status, ['Content-Type' => 'application/json'], json_encode([
        'status' => $status,
        'message' => $message,
    ], JSON_UNESCAPED_UNICODE));
}

function amisSuccess(string|array $message, int $status = 0)
{
    return json([
        'status' => $status,
        'message' => $message,
    ]);
}

function amisError(string|array $message, int $status = 400)
{
    return json([
        'status' => $status,
        'message' => $message,
    ]);
}

/**
 * 数组转树
 * @param $list
 * @param string $id
 * @param string $pid
 * @param string $son
 * @return array
 */
function arr2tree($list, string $id = 'id', string $pid = 'parent_id', string $son = 'children'): array
{

    if (!is_array($list)) {
        $list = collect($list)->toArray();
    }

    [$tree, $map] = [[], []];
    foreach ($list as $item) {
        $map[$item[$id]] = $item;
    }

    foreach ($list as $item) {
        if (isset($item[$pid], $map[$item[$pid]])) {
            $map[$item[$pid]][$son][] = &$map[$item[$id]];
        } else {
            $tree[] = &$map[$item[$id]];
        }
    }
    unset($map);
    return $tree;
}

/**
 * 树转数组
 * @param $tree
 * @param string $son
 * @return array
 */
function tree2arr($tree, string $son = 'children'): array
{
    $list = [];
    foreach ($tree as $item) {
        $list[] = $item;
        if (isset($item[$son])) {
            $list = array_merge($list, tree2arr($item[$son], $son));
        }
    }
    return $list;
}


/**
 * 查找树的子节点
 * @param array $list
 * @param $id
 * @param string $son
 * @return array
 */
function findTreeChildren(array $list, $id, string $son = 'children'): array
{
    foreach ($list as $item) {
        if ($item['id'] == $id) {

            return $item[$son] ?? [];
        }
        if (isset($item[$son])) {
            $children = findTreeChildren($item[$son], $id, $son);
            if ($children) {
                return $children;
            }
        }
    }
    return [];
}

/**
 * 获取资源路由的中文名称
 * @param string $name
 * @return string
 */
function getNameByResourceRoute(string $name): string
{
    if (str_ends_with($name, '.index')) {
        return '列表';
    }
    if (str_ends_with($name, '.create')) {
        return '创建';
    }
    if (str_ends_with($name, '.edit')) {
        return '编辑';
    }
    if (str_ends_with($name, '.destroy')) {
        return '删除';
    }
    if (str_ends_with($name, '.show')) {
        return '详情';
    }
    if (str_ends_with($name, '.store')) {
        return '保存';
    }
    if (str_ends_with($name, '.update')) {
        return '更新';
    }
    return '';
}

function vite_assets(): HtmlString
{
    $viteUrl = getenv("VITE_URL");
    if ($viteUrl) {
        if (!str_ends_with($viteUrl, '/')) {
            $viteUrl .= "/";
        }
        return new HtmlString(<<<HTML
            <script type="module" src="$viteUrl@vite/client"></script>
            <script type="module" src="{$viteUrl}src/main.ts"></script>
        HTML
        );
    }

    return new HtmlString(<<<HTML
    <script type="module" crossorigin src="/admin/assets/4Kd19hFo.js"></script>
    <link rel="modulepreload" crossorigin href="/admin/assets/B4k84dsy.js">
    <link rel="stylesheet" crossorigin href="/admin/assets/DDgZjSzH.css">
    <link rel="stylesheet" crossorigin href="/admin/assets/Dr7rAe4W.css">
    HTML
    );
}