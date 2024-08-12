<?php

namespace smallruraldog\admin\renderer\action;

use smallruraldog\admin\renderer\Button;

/**
 * @method $this to($v)
 * @method $this cc($v)
 * @method $this bcc($v)
 * @method $this subject($v)
 * @method $this body($v)
 */
class EmailAction extends Button
{
    public string $actionType = 'email';
}
