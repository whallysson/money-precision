<?php

declare(strict_types=1);

namespace Whallysson\Money\Currency;

/**
 * Class AbstractCurrency
 *
 * @author Whallysson Avelino <whallysson.dev@gmail.com>
 */
class AbstractCurrency implements CurrencyInterface
{
    public function __construct(
        protected string $code,
        protected string $symbol,
        protected string $decimalSeparator,
        protected string $thousandsSeparator,
        protected string $symbolPosition = 'before'
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
}
