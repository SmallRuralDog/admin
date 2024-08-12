<?php
namespace smallruraldog\admin\renderer\action;
use smallruraldog\admin\renderer\Button;

/**
 * @method $this link($v)
 */
class LinkAction extends Button
{
    public string $actionType = 'link';
}
