<?php

namespace smallruraldog\admin\renderer\form;

/**
 * @method self body( $v) 表单项集合
 * @method self label( $v) group 的标签
 * @method self gap($v) 表单项之间的间距，可选：xs、sm、normal
 * @method self direction($v) 可以配置水平展示还是垂直展示。对应的配置项分别是：vertical、horizontal
 * @method self subFormMode($v)
 * @method self subFormHorizontal($v)
 */
class Group extends FormBase
{
    public string $type = 'group';

    public function directionVertical(): static
    {
        $this->direction('vertical');
        return $this;
    }
}
