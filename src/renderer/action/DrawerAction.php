<?php

namespace smallruraldog\admin\renderer\action;

use smallruraldog\admin\renderer\Button;

/**
 * @method $this drawer($v)
 * @method $this nextCondition($v)
 * @method $this reload($v)
 * @method $this redirect($v)
 */
class DrawerAction extends Button
{
    public string $actionType = 'drawer';
}
