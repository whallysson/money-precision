<?php

declare(strict_types=1);

namespace Whallysson\Money\Currency;

/**
 * Class AbstractCurrency
 *
 * @author Whallysson Avelino <whallysson.dev@gmail.com>
 * @package Whallysson\Money\Currency
 */
class AbstractCurrency implements CurrencyInterface
{
    /** @var string */
    protected string $code;

    /** @var string */
    protected string $symbol;

    /** @var string */
    protected string $decimalSeparator;

    /** @var string */
    protected string $thousandsSeparator;

    /** @var string */
    protected string $symbolPosition;

    public function __construct(
        string $code,
        string $symbol,
        string $decimalSeparator,
        string $thousandsSeparator,
        string $symbolPosition = 'before'
    ) {
        $this->code = $code;
        $this->symbol = $symbol;
        $this->decimalSeparator = $decimalSeparator;
        $this->thousandsSeparator = $thousandsSeparator;
        $this->symbolPosition = $symbolPosition;
    }

    /**
     * @return string
     */
    public function getSymbol(): string
    {
        return $this->symbol;
    }

    /**
     * @return string
     */
    public function getDecimalSeparator(): string
    {
        return $this->decimalSeparator;
    }

    /**
     * @return string
     */
    public function getThousandsSeparator(): string
    {
        return $this->thousandsSeparator;
    }

    /**
     * @return string
     */
    public function getSymbolPosition(): string
    {
        return $this->symbolPosition;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }
}
