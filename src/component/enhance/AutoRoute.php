<?php

namespace smallruraldog\admin\component\enhance;

class AutoRoute
{
    use AutoRouteAction;

    public static function make(): static
    {
        return new static();
    }
}