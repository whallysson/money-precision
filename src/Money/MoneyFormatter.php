<?php

declare(strict_types=1);

namespace Whallysson\Money\Money;

use Whallysson\Money\Currency\CurrencyInterface;
use Whallysson\Money\Formatter\FormatterInterface;

/**
 * Class MoneyFormatter
 *
 * @author Whallysson Avelino <whallysson.dev@gmail.com>
 * @package Whallysson\Money\Money
 */
class MoneyFormatter implements FormatterInterface
{
    /**
     * @param string $value
     * @param CurrencyInterface $currency
     * @param bool $showSymbol
     * @param bool $showThousandsSeparator
     * @return string
     */
    public function format(
        string $value,
        CurrencyInterface $currency,
        bool $showSymbol = true,
        bool $showThousandsSeparator = true
    ): string {
        $formattedNumber = number_format(
            (float)$value,
            2,
            $currency->getDecimalSeparator(),
            $showThousandsSeparator ? $currency->getThousandsSeparator() : ''
        );

        if (!$showSymbol) {
            return $formattedNumber;
        }

        return match($currency->getSymbolPosition(), [
            'before' => sprintf('%s %s', $currency->getSymbol(), $formattedNumber),
            'after' => sprintf('%s %s', $formattedNumber, $currency->getSymbol()),
        ], sprintf('%s %s', $currency->getSymbol(), $formattedNumber));
    }
}
