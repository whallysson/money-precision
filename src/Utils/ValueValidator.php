<?php

declare(strict_types=1);

namespace Whallysson\Money\Utils;

use Whallysson\Money\Money\MoneyInterface;

/**
 * Class ValueValidator
 *
 * @author Whallysson Avelino <whallysson.dev@gmail.com>
 */
class ValueValidator
{
    public static function validate(mixed $value): void
    {
        if (! ($value instanceof MoneyInterface) && ! is_int($value) && ! is_float($value) && ! is_string($value)) {
            throw new \InvalidArgumentException('Invalid value type');
        }
    }
}
