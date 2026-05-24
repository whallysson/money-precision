<?php

declare(strict_types=1);

namespace Whallysson\Money\Money;

use Whallysson\Money\Currency\CurrencyInterface;
use Whallysson\Money\RoundingMode;

/**
 * Interface MoneyInterface
 *
 * @author Whallysson Avelino <whallysson.dev@gmail.com>
 */
interface MoneyInterface
{
    public function int(): int;

    public function toCents(): int;

    public function toMinorUnits(): int;

    public function decimal(int $precision = 2): string;

    public function toDecimal(?int $precision = null): string;

    public function currency(): CurrencyInterface;

    public function add(self $money): self;

    public function sub(self $money): self;

    public function mul(int|string $multiplier, RoundingMode $roundingMode): self;

    public function div(int|string $divisor, RoundingMode $roundingMode): self;

    public function compare(self $money): int;

    public function equals(self $money): bool;

    public function greaterThan(self $money): bool;

    public function lessThan(self $money): bool;

    public function isDecimal(): bool;

    public function format(
        bool $showSymbol = true,
        bool $showThousandsSeparator = true
    ): string;
}
