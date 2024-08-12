<?php

namespace smallruraldog\admin\renderer\form;

/**
 * @method $this extractValue($v)
 * @method $this joinValues($v)
 * @method $this delimiter($v)
 * @method $this allowCity($v)
 * @method $this allowDistrict($v)
 * @method $this allowStreet($v)
 * @method $this searchable($v)
 */
class InputCity extends FormBase
{
    public string $type = 'input-city';
}
