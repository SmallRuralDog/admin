<?php

namespace smallruraldog\admin\renderer\action;

use smallruraldog\admin\renderer\Button;

/**
 * @method $this api($v)
 * @method $this feedback($v)
 * @method $this reload($v)
 * @method $this redirect($v)
 * @method $this ignoreConfirm($v)
 */
class AjaxAction extends Button
{
    public string $actionType = 'ajax';
}
