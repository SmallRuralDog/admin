<?php

namespace smallruraldog\admin\renderer\table;

use smallruraldog\admin\renderer\BaseSchema;

/**
 * 表格列，不指定类型时默认为文本类型。
 * @method self label($v)
 * @method self fixed($v)
 * @method self name($v)
 * @method self popOver($v)
 * @method self quickEdit($v)
 * @method self quickEditOnUpdate($v)
 * @method self copyable($v)
 * @method self sortable($v)
 * @method self searchable($v)
 * @method self toggled($v)
 * @method self width($v)
 * @method self align($v)
 * @method self className($v)
 * @method self classNameExpr($v)
 * @method self labelClassName($v)
 * @method self filterable($v)
 * @method self breakpoint($v)
 * @method self remark($v)
 * @method self value($v)
 * @method self unique($v)
 */
class TableColumn extends BaseSchema
{
    public string $type = 'text';

}
