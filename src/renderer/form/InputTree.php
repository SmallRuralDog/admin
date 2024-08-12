<?php

namespace smallruraldog\admin\renderer\form;

/**
 * @method self hideRoot($v)
 * @method self rootLabel($v)
 * @method self rootValue($v)
 * @method self showIcon($v)
 * @method self autoCheckChildren($v)
 * @method self cascade($v)
 * @method self withChildren($v)
 * @method self onlyChildren($v)
 * @method self rootCreatable($v)
 * @method self enableNodePath($v)
 * @method self pathSeparator($v)
 * @method self showOutline($v)
 * @method self searchable($v)
 * @method self initiallyOpen($v)
 * @method self unfoldedLevel($v)
 * @method self treeContainerClassName($v)
 * @method self deferApi($v)
 * @method self menuTpl($v)
 */
class InputTree extends FormOptions
{
    public string $type = 'input-tree';

}
