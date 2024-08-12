<?php

namespace smallruraldog\admin\component\form;

use Closure;
use Illuminate\Support\Str;
use Respect\Validation\Rules;
use Respect\Validation\Validator;
use smallruraldog\admin\renderer\form\FormBase;
use stdClass;

/**
 * @method self size($v) 表单项大小 'xs' | 'sm' | 'md' | 'lg' | 'full'
 * @method self label($v) 描述标题
 * @method self labelClassName($v) 配置 label className
 * @method self name($v) 字段名，表单提交时的 key，支持多层级，用.连接，如： a.b.c
 * @method self remark($v) 显示一个小图标, 鼠标放上去的时候显示提示内容
 * @method self clearable($v)
 * @method self labelRemark($v) 显示一个小图标, 鼠标放上去的时候显示提示内容, 这个小图标跟 label 在一起
 * @method self hint($v) 输入提示，聚焦的时候显示
 * @method self submitOnChange($v) 当修改完的时候是否提交表单。
 * @method self readOnly($v) 是否只读
 * @method self readOnlyOn($v) 只读条件
 * @method self validateOnChange($v) 不设置时，当表单提交过后表单项每次修改都会触发重新验证，如果设置了，则由此配置项来决定要不要每次修改都触发验证。
 * @method self description($v) 描述内容，支持 Html 片段。
 * @method self desc($v) 用 description 代替
 * @method self descriptionClassName($v) 配置描述上的 className
 * @method self mode($v) 配置当前表单项展示模式 'normal' | 'inline' | 'horizontal'
 * @method self horizontal($v) 当配置为水平布局的时候，用来配置具体的左右分配。
 * @method self inline($v) 表单 control 是否为 inline 模式。
 * @method self inputClassName($v) 配置 input className
 * @method self placeholder($v) 占位符
 * @method self validationErrors($v) 验证失败的提示信息
 * @method self validations($v)
 * @method self value($v) 默认值
 * @method self clearValueOnHidden($v) 表单项隐藏时，是否在当前 Form 中删除掉该表单项值。注意同名的未隐藏的表单项值也会删掉
 * @method self validateApi($v) 远端校验表单项接口
 *
 * @method self columnRatio($v) 宽度占用比率。在某些容器里面有用比如 group
 */
class Item extends stdClass
{
    use ItemValidator;

    protected string $label;
    protected string $name;


    protected FormBase $formItem;

    public function __construct($name, $label)
    {
        if (empty($label)) {
            $label = Str::studly($name);
        }
        $this->name = $name;
        $this->label = $label;
        $this->rule = new Validator;
        $this->formItem = FormBase::make()->label($label)->name($name);
    }

    public static function make($name, $label): static
    {
        return new static($name, $label);
    }

    public function __call($name, $arguments)
    {
        $this->formItem->$name(...$arguments);
        return $this;
    }

    /**
     * 设置表单项,返回的是表单项Amis对象
     * @param $typeComponent
     * @return FormBase
     */
    public function useFormItem($typeComponent = null)
    {
        if ($typeComponent) {
            if ($typeComponent instanceof Closure) {
                $typeComponent = $typeComponent();
            }
            //组合已有的属性
            foreach ($this->formItem as $key => $value) {
                if (!property_exists($typeComponent, $key)) {
                    $typeComponent->$key = $value;
                }
            }
            //替换formItem
            $this->formItem = $typeComponent;
        }

        foreach ($this->rule->getRules() as $rule) {
            $validations = collect();
            if ($rule instanceof Rules\NotEmpty) {
                $this->formItem->required(true);
            }

            $this->formItem->validations($validations->join(','));
        }

        return $this->formItem;
    }

    /**
     * 获取name
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return mixed|string
     */
    public function getLabel(): mixed
    {
        return $this->label;
    }

    /**
     * 渲染
     * @return FormBase
     */
    public function render(): FormBase
    {
        return $this->formItem;
    }

}