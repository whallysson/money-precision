<?php

declare(strict_types=1);

namespace Whallysson\Money\Currency;

/**
 * Interface CurrencyInterface
 *
 * @author Whallysson Avelino <whallysson.dev@gmail.com>
 */
interface CurrencyInterface
{
    public function getSymbol(): string;

    public function getDecimalSeparator(): string;

    public function getThousandsSeparator(): string;

    public function getSymbolPosition(): string;

    public function getCode(): string;
}
