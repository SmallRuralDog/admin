<?php

namespace smallruraldog\admin\middleware;

use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

class DomainCheck implements MiddlewareInterface
{

    public function process(Request $request, callable $handler): Response
    {
        $domain = $request->host();
        $adminDomain = admin_config('domain');
        if ($adminDomain && $adminDomain !== $domain) {
            return jsonError('Domain not allowed', 404);
        }
        return $handler($request);

    }
}