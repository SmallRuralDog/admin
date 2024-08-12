<?php

namespace smallruraldog\admin\renderer\action;

use smallruraldog\admin\renderer\Button;

/**
 * @method $this dialog($v)
 * @method $this nextCondition($v)
 * @method $this reload($v)
 * @method $this redirect($v)
 */
class DialogAction extends Button
{
    public string $actionType = 'dialog';
}
