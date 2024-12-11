<?php

declare(strict_types=1);

namespace Whallysson\Money\Money;

/**
 * Interface MoneyInterface
 *
 * @author Whallysson Avelino <whallysson.dev@gmail.com>
 */
interface MoneyInterface
{
    /**
     * @param  int|float|string  $value
     */
    public function amount($value): self;

    public function int(): int;

    public function decimal(int $precision = 2): string;

    /**
     * @param  MoneyInterface|int|float|string  $value
     */
    public function add($value): self;

    /**
     * @param  MoneyInterface|int|float|string  $value
     */
    public function sub($value): self;

    /**
     * @param  MoneyInterface|int|float|string  $value
     */
    public function mul($value): self;

    /**
     * @param  MoneyInterface|int|float|string  $value
     */
    public function div($value): self;

    /**
     * @param  MoneyInterface|int|float|string  $value
     */
    public function equals($value): bool;

    /**
     * @param  MoneyInterface|int|float|string  $value
     */
    public function greaterThan($value): bool;

    /**
     * @param  MoneyInterface|int|float|string  $value
     */
    public function lessThan($value): bool;

    public function isDecimal(): bool;
}
