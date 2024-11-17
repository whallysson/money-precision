<?php

declare(strict_types=1);

namespace Whallysson\Money\Currency;

/**
 * Interface CurrencyInterface
 *
 * @author Whallysson Avelino <whallysson.dev@gmail.com>
 * @package Whallysson\Money\Currency
 */
interface CurrencyInterface
{
    /**
     * @return string
     */
    public function getSymbol(): string;

    /**
     * @return string
     */
    public function getDecimalSeparator(): string;

    /**
     * @return string
     */
    public function getThousandsSeparator(): string;

    /**
     * @return string
     */
    public function getSymbolPosition(): string;

    /**
     * @return string
     */
    public function getCode(): string;
}
