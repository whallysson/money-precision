<?php

declare(strict_types=1);

namespace Whallysson\Money;

use Whallysson\Money\Currency\CurrencyFactory;
use Whallysson\Money\Currency\CurrencyInterface;
use Whallysson\Money\Formatter\FormatterInterface;
use Whallysson\Money\Money\MoneyFormatter;
use Whallysson\Money\Money\MoneyInterface;

/**
 * Class Money
 *
 * @author Whallysson Avelino <whallysson.dev@gmail.com>
 */
final class Money implements MoneyInterface
{
    private function __construct(
        private readonly int $minorUnits,
        private readonly CurrencyInterface $currency
    ) {}

    public static function fromCents(int $cents, string|CurrencyInterface $currency = 'BRL'): self
    {
        return new self($cents, self::resolveCurrency($currency));
    }

    public static function fromMinorUnits(int $minorUnits, string|CurrencyInterface $currency = 'BRL'): self
    {
        return self::fromCents($minorUnits, $currency);
    }

    public static function fromDecimal(string $amount, string|CurrencyInterface $currency = 'BRL'): self
    {
        $resolvedCurrency = self::resolveCurrency($currency);

        return new self(
            self::decimalToMinorUnits($amount, $resolvedCurrency->getFractionDigits()),
            $resolvedCurrency
        );
    }

    public static function parse(string $amount, string|CurrencyInterface $currency = 'BRL'): self
    {
        $resolvedCurrency = self::resolveCurrency($currency);
        $normalizedAmount = preg_replace('/\s+/u', '', trim($amount));

        if ($normalizedAmount === null || $normalizedAmount === '') {
            throw new \InvalidArgumentException('Invalid money format');
        }

        $symbol = $resolvedCurrency->getSymbol();

        if ($symbol !== '') {
            $normalizedAmount = str_replace($symbol, '', $normalizedAmount);
        }

        $thousandsSeparator = $resolvedCurrency->getThousandsSeparator();

        if ($thousandsSeparator !== '') {
            $normalizedAmount = str_replace($thousandsSeparator, '', $normalizedAmount);
        }

        $decimalSeparator = $resolvedCurrency->getDecimalSeparator();

        if ($decimalSeparator !== '.') {
            $normalizedAmount = str_replace($decimalSeparator, '.', $normalizedAmount);
        }

        return self::fromDecimal($normalizedAmount, $resolvedCurrency);
    }

    public function int(): int
    {
        return $this->minorUnits;
    }

    public function toCents(): int
    {
        return $this->minorUnits;
    }

    public function toMinorUnits(): int
    {
        return $this->minorUnits;
    }

    public function decimal(int $precision = 2): string
    {
        return $this->toDecimal($precision);
    }

    public function toDecimal(?int $precision = null): string
    {
        $fractionDigits = $this->currency->getFractionDigits();
        $precision ??= $fractionDigits;

        if ($precision < 0) {
            throw new \InvalidArgumentException('Precision must be greater than or equal to zero');
        }

        if ($precision < $fractionDigits) {
            throw new \InvalidArgumentException('Precision cannot be lower than currency fraction digits');
        }

        return $this->minorUnitsToDecimal($this->minorUnits, $fractionDigits, $precision);
    }

    public function currency(): CurrencyInterface
    {
        return $this->currency;
    }

    public function add(MoneyInterface $money): self
    {
        $this->assertSameCurrency($money);

        return new self(
            self::integerStringToInt(bcadd((string) $this->minorUnits, (string) $money->toCents(), 0)),
            $this->currency
        );
    }

    public function sub(MoneyInterface $money): self
    {
        $this->assertSameCurrency($money);

        return new self(
            self::integerStringToInt(bcsub((string) $this->minorUnits, (string) $money->toCents(), 0)),
            $this->currency
        );
    }

    public function mul(int|string $multiplier, RoundingMode $roundingMode): self
    {
        $ratio = $this->decimalRatio($multiplier, 'Multiplier');
        $numerator = bcmul((string) $this->minorUnits, (string) $ratio['numerator'], 0);

        return new self(
            $this->divideAndRound($numerator, (string) $ratio['denominator'], $roundingMode),
            $this->currency
        );
    }

    public function div(int|string $divisor, RoundingMode $roundingMode): self
    {
        $ratio = $this->decimalRatio($divisor, 'Divisor');

        if ($ratio['numerator'] === 0) {
            throw new \InvalidArgumentException('Division by zero');
        }

        $numerator = bcmul((string) $this->minorUnits, (string) $ratio['denominator'], 0);

        return new self(
            $this->divideAndRound($numerator, (string) $ratio['numerator'], $roundingMode),
            $this->currency
        );
    }

    public function compare(MoneyInterface $money): int
    {
        $this->assertSameCurrency($money);

        return bccomp((string) $this->minorUnits, (string) $money->toCents(), 0);
    }

    public function equals(MoneyInterface $money): bool
    {
        return $this->compare($money) === 0;
    }

    public function greaterThan(MoneyInterface $money): bool
    {
        return $this->compare($money) === 1;
    }

    public function lessThan(MoneyInterface $money): bool
    {
        return $this->compare($money) === -1;
    }

    public function isDecimal(): bool
    {
        return true;
    }

    public function format(
        bool $showSymbol = true,
        bool $showThousandsSeparator = true
    ): string {
        return $this->formatter()->format(
            $this->minorUnits,
            $this->currency,
            $showSymbol,
            $showThousandsSeparator
        );
    }

    private static function resolveCurrency(string|CurrencyInterface $currency): CurrencyInterface
    {
        return $currency instanceof CurrencyInterface ? $currency : CurrencyFactory::get($currency);
    }

    private static function decimalToMinorUnits(string $amount, int $fractionDigits): int
    {
        $amount = trim($amount);

        if (preg_match('/^-?\d+(?:\.\d+)?$/', $amount) !== 1) {
            throw new \InvalidArgumentException('Invalid decimal money format');
        }

        $isNegative = str_starts_with($amount, '-');
        $unsignedAmount = $isNegative ? substr($amount, 1) : $amount;
        $parts = explode('.', $unsignedAmount, 2);
        $integerPart = $parts[0];
        $fractionPart = $parts[1] ?? '';

        if (strlen($fractionPart) > $fractionDigits) {
            throw new \InvalidArgumentException('Decimal money format exceeds currency fraction digits');
        }

        $fractionPart = str_pad($fractionPart, $fractionDigits, '0');

        return self::integerStringToInt(($isNegative ? '-' : '').$integerPart.$fractionPart);
    }

    private function minorUnitsToDecimal(int $minorUnits, int $fractionDigits, int $precision): string
    {
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

        $fractionPart = str_pad($fractionPart, $precision, '0');
        $sign = $negative ? '-' : '';

        if ($precision === 0) {
            return $sign.$integerPart;
        }

        return $sign.$integerPart.'.'.$fractionPart;
    }

    /**
     * @return array{numerator: int, denominator: int}
     */
    private function decimalRatio(int|string $value, string $label): array
    {
        $value = is_int($value) ? (string) $value : trim($value);

        if (preg_match('/^-?\d+(?:\.\d+)?$/', $value) !== 1) {
            throw new \InvalidArgumentException($label.' must be an integer or decimal string');
        }

        $isNegative = str_starts_with($value, '-');
        $unsignedValue = $isNegative ? substr($value, 1) : $value;
        $parts = explode('.', $unsignedValue, 2);
        $integerPart = $parts[0];
        $fractionPart = $parts[1] ?? '';
        $digits = ltrim($integerPart.$fractionPart, '0');
        $numerator = $digits === '' ? '0' : ($isNegative ? '-' : '').$digits;
        $denominator = '1'.str_repeat('0', strlen($fractionPart));

        return [
            'numerator' => self::integerStringToInt($numerator),
            'denominator' => self::integerStringToInt($denominator),
        ];
    }

    private function divideAndRound(string $numerator, string $denominator, RoundingMode $roundingMode): int
    {
        $numerator = self::normalizeIntegerString($numerator);
        $denominator = self::normalizeIntegerString($denominator);

        if ($denominator === '0') {
            throw new \InvalidArgumentException('Division by zero');
        }

        $isNegative = str_starts_with($numerator, '-') !== str_starts_with($denominator, '-');
        $absoluteNumerator = $this->absoluteIntegerString($numerator);
        $absoluteDenominator = $this->absoluteIntegerString($denominator);
        $quotient = self::normalizeIntegerString(bcdiv($absoluteNumerator, $absoluteDenominator, 0));
        $remainder = self::normalizeIntegerString(bcmod($absoluteNumerator, $absoluteDenominator));
        $hasRemainder = $remainder !== '0';

        $shouldIncrement = match ($roundingMode) {
            RoundingMode::TowardsZero => false,
            RoundingMode::AwayFromZero => $hasRemainder,
            RoundingMode::PositiveInfinity => ! $isNegative && $hasRemainder,
            RoundingMode::NegativeInfinity => $isNegative && $hasRemainder,
            RoundingMode::HalfAwayFromZero => $this->compareDoubleRemainder($remainder, $absoluteDenominator) >= 0,
            RoundingMode::HalfTowardsZero => $this->compareDoubleRemainder($remainder, $absoluteDenominator) > 0,
            RoundingMode::HalfEven => $this->shouldRoundHalfEven($quotient, $remainder, $absoluteDenominator),
        };

        $rounded = $shouldIncrement ? $this->incrementUnsignedIntegerString($quotient) : $quotient;

        if ($rounded === '0') {
            return 0;
        }

        return self::integerStringToInt(($isNegative ? '-' : '').$rounded);
    }

    private function compareDoubleRemainder(string $remainder, string $denominator): int
    {
        return self::compareUnsignedIntegerStrings(
            $this->doubleUnsignedIntegerString($remainder),
            $denominator
        );
    }

    private function shouldRoundHalfEven(string $quotient, string $remainder, string $denominator): bool
    {
        $comparison = $this->compareDoubleRemainder($remainder, $denominator);

        if ($comparison > 0) {
            return true;
        }

        if ($comparison < 0) {
            return false;
        }

        return ((int) substr($quotient, -1)) % 2 === 1;
    }

    private function assertSameCurrency(MoneyInterface $money): void
    {
        if ($this->currency->getCode() !== $money->currency()->getCode()) {
            throw new \InvalidArgumentException(sprintf(
                'Cannot operate with different currencies: %s and %s',
                $this->currency->getCode(),
                $money->currency()->getCode()
            ));
        }
    }

    private function formatter(): FormatterInterface
    {
        return new MoneyFormatter;
    }

    private function absoluteIntegerString(string $value): string
    {
        return str_starts_with($value, '-') ? substr($value, 1) : $value;
    }

    private static function normalizeIntegerString(string $value): string
    {
        $value = trim($value);

        if (preg_match('/^-?\d+$/', $value) !== 1) {
            throw new \InvalidArgumentException('Invalid integer money format');
        }

        $isNegative = str_starts_with($value, '-');
        $digits = ltrim($value, '+-');
        $digits = ltrim($digits, '0');

        if ($digits === '') {
            return '0';
        }

        return ($isNegative ? '-' : '').$digits;
    }

    private function doubleUnsignedIntegerString(string $value): string
    {
        $value = ltrim($value, '0');

        if ($value === '') {
            return '0';
        }

        $result = '';
        $carry = 0;

        for ($index = strlen($value) - 1; $index >= 0; $index--) {
            $digit = ((int) $value[$index]) * 2 + $carry;
            $result = ($digit % 10).$result;
            $carry = intdiv($digit, 10);
        }

        return $carry > 0 ? $carry.$result : $result;
    }

    private function incrementUnsignedIntegerString(string $value): string
    {
        $value = ltrim($value, '0');

        if ($value === '') {
            return '1';
        }

        $result = '';
        $carry = 1;

        for ($index = strlen($value) - 1; $index >= 0; $index--) {
            $digit = ((int) $value[$index]) + $carry;
            $result = ($digit % 10).$result;
            $carry = intdiv($digit, 10);
        }

        return $carry > 0 ? $carry.$result : $result;
    }

    private static function integerStringToInt(string $value): int
    {
        $normalizedValue = self::normalizeIntegerString($value);
        $digits = ltrim($normalizedValue, '-');

        if (self::compareUnsignedIntegerStrings($digits, (string) PHP_INT_MAX) > 0) {
            throw new \InvalidArgumentException('Money amount exceeds PHP integer range');
        }

        return (int) $normalizedValue;
    }

    private static function compareUnsignedIntegerStrings(string $left, string $right): int
    {
        $left = ltrim($left, '0');
        $right = ltrim($right, '0');
        $left = $left === '' ? '0' : $left;
        $right = $right === '' ? '0' : $right;

        if (strlen($left) > strlen($right)) {
            return 1;
        }

        if (strlen($left) < strlen($right)) {
            return -1;
        }

        return $left <=> $right;
    }
}
