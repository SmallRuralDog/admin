<?php

namespace smallruraldog\admin\renderer\action;

use smallruraldog\admin\renderer\Button;

/**
 * @method $this blank($v)
 * @method $this url($v)
 */
class UrlAction extends Button
{
    public string $actionType = 'url';
}
