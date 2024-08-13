<?php

namespace smallruraldog\admin\component\form;

use Closure;
use Respect\Validation\Validator;

trait ItemValidator
{

    /**
     * @var Validator
     *
     */
    protected Validator $rule;

    /**
     * 设置验证规则
     * 验证规则参见 https://respect-validation.readthedocs.io/en/2.0/list-of-rules/
     * @param Closure $rule
     * @return ItemValidator|Item
     */
    public function rule(Closure $rule): self
    {
        $rule($this->rule);
        return $this;
    }

    /**
     * 获取验证规则
     */
    public function getRule(): Validator
    {
        return $this->rule;
    }

    /**
     * 不能为空
     */
    public function required(bool $enable = true): self
    {

        if ($enable) $this->rule->notEmpty();
        return $this;
    }

    //Alnum() 只包含字母和数字

    /**只包含字母和数字*/
    public function alnum(bool $enable = true): self
    {

        if ($enable) $this->rule->alnum();
        return $this;
    }

    //Alpha() 只包含字母

    /**只包含字母*/
    public function alpha(bool $enable = true): self
    {
        if ($enable) $this->rule->alpha();
        return $this;
    }

    //ArrayType() 数组类型

    /**数组类型*/
    public function arrayType(bool $enable = true): self
    {
        if ($enable) $this->rule->arrayType();
        return $this;
    }
    //Between(mixed $minimum, mixed $maximum) 验证输入是否在其他两个值之间。

    /**验证输入是否在其他两个值之间*/
    public function between($minimum, $maximum, bool $enable = true): self
    {
        if ($enable) $this->rule->between($minimum, $maximum);
        return $this;
    }
    //BoolType() 验证是否是布尔型

    /**验证是否是布尔型*/
    public function boolType(bool $enable = true): self
    {
        if ($enable) $this->rule->boolType();
        return $this;
    }
    //Contains(mixed $expectedValue) 验证输入是否包含某些值

    /**验证输入是否包含某些值*/
    public function contains($expectedValue, bool $enable = true): self
    {
        if ($enable) $this->rule->contains($expectedValue);
        return $this;
    }
    //ContainsAny(array $needles) 验证输入是否至少包含一个定义的值

    /**验证输入是否至少包含一个定义的值*/
    public function containsAny(array $needles, bool $enable = true): self
    {
        if ($enable) $this->rule->containsAny($needles);
        return $this;
    }
    //Digit() 验证输入是否只包含数字

    /**验证输入是否只包含数字*/
    public function digit(bool $enable = true): self
    {
        if ($enable) $this->rule->digit();
        return $this;
    }
    //Domain() 验证是否是合法的域名

    /**验证是否是合法的域名*/
    public function domain(bool $enable = true): self
    {
        if ($enable) $this->rule->domain();
        return $this;
    }
    //Email() 验证是否是合法的邮件地址

    /**验证是否是合法的邮件地址*/
    public function email(bool $enable = true): self
    {
        if ($enable) $this->rule->email();
        return $this;
    }
    //Extension(string $extension) 验证后缀名

    /**验证后缀名*/
    public function extension(string $extension, bool $enable = true): self
    {
        if ($enable) $this->rule->extension($extension);
        return $this;
    }
    //FloatType() 验证是否是浮点型

    /**验证是否是浮点型*/
    public function floatType(bool $enable = true): self
    {
        if ($enable) $this->rule->floatType();
        return $this;
    }
    //IntType() 验证是否是整数

    /**验证是否是整数*/
    public function intType(bool $enable = true): self
    {
        if ($enable) $this->rule->intType();
        return $this;
    }
    //In() 验证是否在给定的值中

    /**验证是否在给定的值中*/
    public function in(array $haystack, bool $enable = true): self
    {
        if ($enable) $this->rule->in($haystack);
        return $this;
    }

    //Ip() 验证是否是ip地址

    /**验证是否是ip地址*/
    public function ip(bool $enable = true): self
    {
        if ($enable) $this->rule->ip();
        return $this;
    }
    //Json() 验证是否是json数据

    /**验证是否是json数据*/
    public function json(bool $enable = true): self
    {
        if ($enable) $this->rule->json();
        return $this;
    }
    //Length(int $min, int $max) 验证长度是否在给定区间

    /**验证长度是否在给定区间*/
    public function length(int $min, int $max, bool $enable = true): self
    {
        if ($enable) $this->rule->length($min, $max);
        return $this;
    }
    //LessThan(mixed $compareTo) 验证长度是否小于给定值

    /**验证长度是否小于给定值*/
    public function lessThan($compareTo, bool $enable = true): self
    {
        if ($enable) $this->rule->lessThan($compareTo);
        return $this;
    }
    //Lowercase() 验证是否是小写字母

    /**验证是否是小写字母*/
    public function lowercase(bool $enable = true): self
    {
        if ($enable) $this->rule->lowercase();
        return $this;
    }
    //MacAddress() 验证是否是mac地址

    /**验证是否是mac地址*/
    public function macAddress(bool $enable = true): self
    {
        if ($enable) $this->rule->macAddress();
        return $this;
    }
    //NotEmpty() 验证是否为空

    /**验证是否为空*/
    public function notEmpty(bool $enable = true): self
    {
        if ($enable) $this->rule->notEmpty();
        return $this;
    }
    //NullType() 验证是否为null

    /**验证是否为null*/
    public function nullType(bool $enable = true): self
    {
        if ($enable) $this->rule->nullType();
        return $this;
    }
    //Number() 验证是否为数字

    /**验证是否为数字*/
    public function number(bool $enable = true): self
    {
        if ($enable) $this->rule->number();
        return $this;
    }
    //ObjectType() 验证是否为对象

    /**验证是否为对象*/
    public function objectType(bool $enable = true): self
    {
        if ($enable) $this->rule->objectType();
        return $this;
    }
    //StringType() 验证是否为字符串类型

    /**验证是否为字符串类型*/
    public function stringType(bool $enable = true): self
    {
        if ($enable) $this->rule->stringType();
        return $this;
    }

    //StartsWith(string $start) 验证是否以某个字符串开头

    /**验证是否以某个字符串开头*/
    public function startsWith(string $start, bool $identical = false, bool $enable = true): self
    {
        $enable && $this->rule->startsWith($start, $identical);
        return $this;
    }

    //Url() 验证是否为url

    /**验证是否为url*/
    public function url(bool $enable = true): self
    {
        if ($enable) $this->rule->url();
        return $this;
    }

}