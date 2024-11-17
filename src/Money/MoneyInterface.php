<?php

declare(strict_types=1);

namespace Whallysson\Money\Money;

/**
 * Interface MoneyInterface
 *
 * @author Whallysson Avelino <whallysson.dev@gmail.com>
 * @package Whallysson\Money\Money
 */
interface MoneyInterface
{
    /**
     * @param int|float|string $value
     * @return self
     */
    public function amount($value): self;

    /**
     * @return int
     */
    public function int(): int;

    /**
     * @param int $precision
     * @return string
     */
    public function decimal(int $precision = 2): string;

    /**
     * @param MoneyInterface|int|float|string $value
     * @return self
     */
    public function add($value): self;

    /**
     * @param MoneyInterface|int|float|string $value
     * @return self
     */
    public function sub($value): self;

    /**
     * @param MoneyInterface|int|float|string $value
     * @return self
     */
    public function mul($value): self;

    /**
     * @param MoneyInterface|int|float|string $value
     * @return self
     */
    public function div($value): self;

    /**
     * @param MoneyInterface|int|float|string $value
     * @return bool
     */
    public function equals($value): bool;

    /**
     * @param MoneyInterface|int|float|string $value
     * @return bool
     */
    public function greaterThan($value): bool;

    /**
     * @param MoneyInterface|int|float|string $value
     * @return bool
     */
    public function lessThan($value): bool;
}
