<?php

namespace smallruraldog\admin\enum;

use Exception;

/**
 * @method static self cases()
 * @method static self from(string|int $v)
 * @method static self tryFrom(string|int $v)
 */
trait BaseEnums
{
    public static function names(): array
    {
        $arr = [];
        foreach (self::cases() as $directive) {
            $arr[] = $directive->name;
        }

        return $arr;
    }

    public static function values(): array
    {
        $arr = [];
        foreach (self::cases() as $directive) {
            $arr[] = $directive->value ?? $directive->name;
        }

        return $arr;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): string|int
    {
        return $this->value ?? $this->name;
    }

    public static function fromErr(string|int $v, string $err = 'ERR'): self
    {
        $from = self::tryFrom($v);
        abort_if(is_null($from), 400, $err);
        return $from;
    }
}
