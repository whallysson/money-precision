<?php

declare(strict_types=1);

namespace Whallysson\Money;

use Whallysson\Money\Currency\CurrencyFactory;
use Whallysson\Money\Formatter\FormatterInterface;
use Whallysson\Money\Money\MoneyFormatter;
use Whallysson\Money\Money\MoneyInterface;

/**
 * Class Money
 *
 * @author Whallysson Avelino <whallysson.dev@gmail.com>
 * @package Whallysson\Money
 */
class Money implements MoneyInterface
{
    /** @var string|int */
    private $value;

    /** @var bool */
    private bool $isDecimal;

    /** @var FormatterInterface */
    private FormatterInterface $formatter;

    public function __construct()
    {
        ini_set('precision', '16');
        bcscale(8);
        $this->formatter = new MoneyFormatter();
    }

    /**
     * @inheritDoc
     */
    public function amount($value): MoneyInterface
    {
        if (is_string($value)) {
            $value = str_replace(['R$', '$', '€', ' ', ','], ['', '', '', '', '.'], $value);
        }

        if (!is_numeric($value)) {
            throw new \InvalidArgumentException("Invalid number format");
        }

        $value = (string)$value;
        $this->isDecimal = (strpos($value, '.') !== false);
        $this->value = $value;

        return $this;
    }

    public function int(): int
    {
        if (!$this->isDecimal) {
            return (int)$this->value;
        }

        $parts = explode('.', (string)$this->value);
        $integers = $parts[0];
        $decimals = str_pad(substr($parts[1] . '00', 0, 2), 2, '0');

        return (int)($integers . $decimals);
    }

    public function decimal(int $precision = 2): string
    {
        if ($this->isDecimal) {
            return bcscale($precision) !== 0 ? bcdiv($this->value, '1', $precision) : $this->value;
        }

        $value = str_pad((string)$this->value, 3, '0', STR_PAD_LEFT);
        $decimals = substr($value, -2);
        $integers = substr($value, 0, -2);
        $result = $integers . '.' . $decimals;

        return bcscale($precision) !== 0 ? bcdiv($result, '1', $precision) : $result;
    }

    /**
     * @param string $operation
     * @param MoneyInterface|int|float|string $value
     * @return self
     */
    private function calculate(string $operation, $value): self
    {
        $value1 = $this->decimal();

        if ($value instanceof MoneyInterface) {
            $value2 = $value->decimal();
        } else {
            $value2 = (new self())->amount($value)->decimal();
        }

        $result = match($operation, [
            'add' => bcadd($value1, $value2),
            'sub' => bcsub($value1, $value2),
            'mul' => bcmul($value1, $value2),
            'div' => bcdiv($value1, $value2)
        ], function () {
            throw new \InvalidArgumentException("Invalid operation");
        });

        $this->value = $result;
        $this->isDecimal = true;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function add($value): MoneyInterface
    {
        return $this->calculate('add', $value);
    }

    /**
     * @inheritDoc
     */
    public function sub($value): MoneyInterface
    {
        return $this->calculate('sub', $value);
    }

    /**
     * @inheritDoc
     */
    public function mul($value): MoneyInterface
    {
        return $this->calculate('mul', $value);
    }

    /**
     * @inheritDoc
     */
    public function div($value): MoneyInterface
    {
        return $this->calculate('div', $value);
    }

    /**
     * @param MoneyInterface|int|float|string $value
     * @return int
     */
    public function compare($value): int
    {
        $value1 = $this->decimal();

        if ($value instanceof MoneyInterface) {
            $value2 = $value->decimal();
        } else {
            $value2 = (new self())->amount($value)->decimal();
        }

        return bccomp($value1, $value2, 2);
    }

    /**
     * @inheritDoc
     */
    public function equals($value): bool
    {
        return $this->compare($value) === 0;
    }

    /**
     * @inheritDoc
     */
    public function greaterThan($value): bool
    {
        return $this->compare($value) === 1;
    }

    /**
     * @inheritDoc
     */
    public function lessThan($value): bool
    {
        return $this->compare($value) === -1;
    }

    /**
     * @param string $currency
     * @param bool $showSymbol
     * @param bool $showThousandsSeparator
     * @return string
     */
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
     * @param int|float|string $value
     * @return MoneyInterface
     */
    public static function of($value): MoneyInterface
    {
        return (new self())->amount($value);
    }
}
