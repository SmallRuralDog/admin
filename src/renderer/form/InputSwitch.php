<?php

namespace smallruraldog\admin\renderer\form;

/**
 * @method $this trueValue($v)
 * @method $this falseValue($v)
 * @method $this option($v)
 * @method $this onText($v)
 * @method $this offText($v)
 */
class InputSwitch extends FormBase
{
    public string $type = 'switch';
}
