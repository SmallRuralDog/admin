<?php

namespace smallruraldog\admin\component\grid;

use Closure;
use smallruraldog\admin\component\grid\trait\ColumnDisplay;
use smallruraldog\admin\component\grid\trait\ColumnEdit;
use smallruraldog\admin\renderer\BaseSchema;
use smallruraldog\admin\renderer\table\TableColumn;
use stdClass;

/**
 * @method self type($v)
 * @method self fixed($v) 配置是否固定当前列 'left' | 'right' | 'none'
 * @method self popOver($v) 配置查看详情功能
 * @method self quickEdit($v) 配置快速编辑功能
 * @method self quickEditOnUpdate($v) 作为表单项时，可以单独配置编辑时的快速编辑面板。
 * @method self copyable($v) 配置点击复制功能
 * @method self sortable($v) 配置是否可以排序
 * @method self searchable($v) 是否可快速搜索
 * @method self toggled($v) 配置是否默认展示
 * @method self width($v) 列宽度
 * @method self align($v) 列对齐方式 'left' | 'right' | 'center' | 'justify'
 * @method self className($v) 列样式表
 * @method self classNameExpr($v) 单元格样式表达式
 * @method self labelClassName($v) 列头样式表
 * @method self filterable($v) todo
 * @method self breakpoint($v) 结合表格的 footable 一起使用。填写 *、xs、sm、md、lg指定 footable 的触发条件，可以填写多个用空格隔开
 * @method self remark($v) 提示信息
 * @method self value($v) 默认值, 只有在 inputTable 里面才有用
 * @method self unique($v) 是否唯一, 只有在 inputTable 里面才有用
 *
 * @method getValue($v) 给组件赋值时自定义处理
 * @method setValue($v) 组件赋值提交时自定义处理
 * @method defaultAttr() 可以自定义属性的设置
 */
class Column extends stdClass
{
    use  ColumnDisplay, ColumnEdit;

    protected string $label;
    protected string $name;

    protected BaseSchema $tableColumn;

    public function __construct($name, $label)
    {
        if (empty($label)) {
            $label = $name;
        }
        $this->label = $label;
        $this->name = $name;
        $this->tableColumn = TableColumn::make()->name($name)->label($label);
    }

    public static function make($name, $label): static
    {
        return new static($name, $label);
    }

    public function __call($name, $arguments)
    {
        $this->tableColumn->$name(...$arguments);
        return $this;
    }

    /**
     * 自定义组件
     * @param null $typeComponent
     * @return BaseSchema|TableColumn|null
     */
    public function useTableColumn($typeComponent = null): BaseSchema|TableColumn|null
    {
        if ($typeComponent) {
            if ($typeComponent instanceof Closure) {
                $typeComponent = $typeComponent();
            }
            foreach ($this->tableColumn as $key => $value) {
                if (!property_exists($typeComponent, $key)) {
                    $typeComponent->$key = $value;
                }
            }
            $this->tableColumn = $typeComponent;
        }
        return $this->tableColumn;
    }


    /**获取列名*/
    public function getName(): string
    {
        return $this->name;
    }

    public function render(): BaseSchema //渲染
    {
        return $this->tableColumn;
    }
}