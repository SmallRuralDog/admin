<?php

namespace smallruraldog\admin\renderer\form;

/**
 * @method $this trueValue($v)
 * @method $this falseValue($v)
 * @method $this option($v)
 * @method $this badge($v)
 * @method $this partial($v)
 * @method $this optionType($v)
 * @method $this checked($v)
 */
class Checkbox extends FormBase
{
    public string $type = 'checkbox';

}
