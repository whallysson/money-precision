<?php

declare(strict_types=1);

namespace Whallysson\Money;

use Whallysson\Money\Currency\CurrencyFactory;
use Whallysson\Money\Formatter\FormatterInterface;
use Whallysson\Money\Money\MoneyFormatter;
use Whallysson\Money\Money\MoneyInterface;
use Whallysson\Money\Utils\ValueValidator;

/**
 * Class Money
 *
 * @author Whallysson Avelino <whallysson.dev@gmail.com>
 */
class Money implements MoneyInterface
{
    private ?string $value = null;

    private bool $isDecimal;

    private int $scale = 8;

    private readonly FormatterInterface $formatter;

    public function __construct()
    {
        ini_set('precision', '16');
        bcscale($this->scale);
        $this->formatter = new MoneyFormatter;
    }

    /**
     * {@inheritDoc}
     */
    public function amount($value): MoneyInterface
    {
        if (is_string($value)) {
            $value = str_replace(['R$', '$', '€', ' ', ','], ['', '', '', '', '.'], $value);
        }

        if (! is_numeric($value)) {
            throw new \InvalidArgumentException('Invalid number format');
        }

        $value = (string) $value;
        $this->isDecimal = (str_contains($value, '.'));
        $this->value = $value;

        return $this;
    }

    public function int(): int
    {
        if (! $this->isDecimal) {
            return (int) $this->value;
        }

        $parts = explode('.', (string) $this->value);
        $integers = $parts[0];
        $decimals = str_pad(substr($parts[1].'00', 0, 2), 2, '0');

        return (int) ($integers.$decimals);
    }

    public function decimal(int $precision = 2): string
    {
        if ($this->isDecimal) {
            $result = bcscale($precision) !== 0 ? bcdiv((string) $this->value, '1', $precision) : $this->value;

            return $result ?? '0';
        }

        $value = str_pad((string) $this->value, 3, '0', STR_PAD_LEFT);
        $decimals = substr($value, -2);
        $integers = substr($value, 0, -2);
        $result = $integers.'.'.$decimals;

        return bcscale($precision) !== 0 ? bcdiv($result, '1', $precision) : $result;
    }

    private function calculate(string $operation, float|int|string|MoneyInterface $value): self
    {
        $value1 = $this->decimal();
        $value2 = $this->getValue($value);

        $result = match ($operation) {
            'add' => bcadd($value1, $value2, $this->scale),
            'sub' => bcsub($value1, $value2, $this->scale),
            'mul' => bcmul($value1, $value2, $this->scale),
            'div' => bcdiv($value1, $value2, $this->scale),
            default => throw new \InvalidArgumentException('Invalid operation')
        };

        $this->value = $result;
        $this->isDecimal = true;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function add($value): MoneyInterface
    {
        ValueValidator::validate($value);

        return $this->calculate('add', $value);
    }

    /**
     * {@inheritDoc}
     */
    public function sub($value): MoneyInterface
    {
        ValueValidator::validate($value);

        return $this->calculate('sub', $value);
    }

    /**
     * {@inheritDoc}
     */
    public function mul($value): MoneyInterface
    {
        ValueValidator::validate($value);

        return $this->calculate('mul', $value);
    }

    /**
     * {@inheritDoc}
     */
    public function div($value): MoneyInterface
    {
        ValueValidator::validate($value);

        return $this->calculate('div', $value);
    }

    public function compare(float|int|string|MoneyInterface $value): int
    {
        $value1 = $this->decimal();
        $value2 = $this->getValue($value);

        return bccomp($value1, $value2, 2);
    }

    /**
     * {@inheritDoc}
     */
    public function equals($value): bool
    {
        return $this->compare($value) === 0;
    }

    /**
     * {@inheritDoc}
     */
    public function greaterThan($value): bool
    {
        return $this->compare($value) === 1;
    }

    /**
     * {@inheritDoc}
     */
    public function lessThan($value): bool
    {
        return $this->compare($value) === -1;
    }

    public function format(
        string $currency = 'BRL',
        bool $showSymbol = true,
        bool $showThousandsSeparator = true
    ): string {
        $currency = CurrencyFactory::get($currency);

        return $this->formatter->format(
            $this->decimal(),
            $currency,
            $showSymbol,
            $showThousandsSeparator
        );
    }

    /**
     * @param  int|float|string  $value
     */
    public static function of($value): MoneyInterface
    {
        return (new self)->amount($value);
    }

    public function isDecimal(): bool
    {
        return $this->isDecimal;
    }

    private function getValue(float|int|string|MoneyInterface $value): string
    {
        ValueValidator::validate($value);

        if ($value instanceof MoneyInterface) {
            return $value->isDecimal() ? $value->decimal() : (string) $value->int();
        }

        $obj = (new self)->amount($value);

        return $obj->isDecimal() ? $obj->decimal() : (string) $obj->int();
    }
}
