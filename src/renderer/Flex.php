<?php

namespace smallruraldog\admin\renderer;

/**
 * @method $this justify($v)
 * @method $this alignItems($v)
 * @method $this alignContent($v)
 * @method $this direction($v)
 * @method $this items($v)
 * @method $this style($v)
 */
class Flex extends BaseSchema
{
    public string $type = 'flex';

    public function __construct()
    {
        $this->justify('start')->alignItems('start');
    }

}
