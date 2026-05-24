<?php

declare(strict_types=1);

namespace Whallysson\Money\Currency;

/**
 * Class AbstractCurrency
 *
 * @author Whallysson Avelino <whallysson.dev@gmail.com>
 */
abstract class AbstractCurrency implements CurrencyInterface
{
    public function __construct(
        protected readonly string $code,
        protected readonly string $symbol,
        protected readonly string $decimalSeparator,
        protected readonly string $thousandsSeparator,
        protected readonly string $symbolPosition = 'before',
        protected readonly int $fractionDigits = 2
    ) {}

    public function getSymbol(): string
    {
        return $this->symbol;
    }

    public function getDecimalSeparator(): string
    {
        return $this->decimalSeparator;
    }

    public function getThousandsSeparator(): string
    {
        return $this->thousandsSeparator;
    }

    public function getSymbolPosition(): string
    {
        return $this->symbolPosition;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getFractionDigits(): int
    {
        return $this->fractionDigits;
    }
}
