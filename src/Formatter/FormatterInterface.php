<?php

declare(strict_types=1);

namespace Whallysson\Money\Formatter;

use Whallysson\Money\Currency\CurrencyInterface;

/**
 * Interface FormatterInterface
 *
 * @author Whallysson Avelino <whallysson.dev@gmail.com>
 */
interface FormatterInterface
{
    public function format(
        string $value,
        CurrencyInterface $currency,
        bool $showSymbol = true,
        bool $showThousandsSeparator = true
    ): string;
}
