<?php

declare(strict_types=1);

namespace Whallysson\Money\Money;

use Whallysson\Money\Currency\CurrencyInterface;
use Whallysson\Money\Formatter\FormatterInterface;

/**
 * Class MoneyFormatter
 *
 * @author Whallysson Avelino <whallysson.dev@gmail.com>
 */
class MoneyFormatter implements FormatterInterface
{
    public function format(
        int $minorUnits,
        CurrencyInterface $currency,
        bool $showSymbol = true,
        bool $showThousandsSeparator = true
    ): string {
        $formattedNumber = $this->formatNumber(
            $minorUnits,
            $currency,
            $showThousandsSeparator
        );

        if (! $showSymbol) {
            return $formattedNumber;
        }

        return match ($currency->getSymbolPosition()) {
            'before' => sprintf('%s %s', $currency->getSymbol(), $formattedNumber),
            'after' => sprintf('%s %s', $formattedNumber, $currency->getSymbol()),
            default => sprintf('%s %s', $currency->getSymbol(), $formattedNumber)
        };
    }

    private function formatNumber(
        int $minorUnits,
        CurrencyInterface $currency,
        bool $showThousandsSeparator
    ): string {
        $fractionDigits = $currency->getFractionDigits();
        $negative = $minorUnits < 0;
        $digits = ltrim((string) $minorUnits, '-');

        if ($fractionDigits > 0) {
            $digits = str_pad($digits, $fractionDigits + 1, '0', STR_PAD_LEFT);
            $integerPart = substr($digits, 0, -$fractionDigits);
            $fractionPart = substr($digits, -$fractionDigits);
        } else {
            $integerPart = $digits;
            $fractionPart = '';
        }

        if ($showThousandsSeparator) {
            $integerPart = $this->groupIntegerPart($integerPart, $currency->getThousandsSeparator());
        }

        $formatted = ($negative ? '-' : '').$integerPart;

        if ($fractionDigits === 0) {
            return $formatted;
        }

        return $formatted.$currency->getDecimalSeparator().$fractionPart;
    }

    private function groupIntegerPart(string $integerPart, string $separator): string
    {
        if ($separator === '') {
            return $integerPart;
        }

        return preg_replace('/\B(?=(\d{3})+(?!\d))/', $separator, $integerPart) ?? $integerPart;
    }
}
