<?php

declare(strict_types=1);

use Whallysson\Money\Currency\AbstractCurrency;
use Whallysson\Money\Currency\Coins\BRL;
use Whallysson\Money\Currency\Coins\EUR;
use Whallysson\Money\Currency\Coins\USD;
use Whallysson\Money\Currency\CurrencyFactory;
use Whallysson\Money\Currency\CurrencyInterface;
use Whallysson\Money\Money;
use Whallysson\Money\Money\MoneyFormatter;
use Whallysson\Money\RoundingMode;

it('separates cents and decimal constructors explicitly', function () {
    expect(Money::fromCents(56)->toDecimal())->toBe('0.56')
        ->and(Money::fromCents(10086)->decimal())->toBe('100.86')
        ->and(Money::fromDecimal('56')->toCents())->toBe(5600)
        ->and(Money::fromDecimal('56.60')->int())->toBe(5660)
        ->and(Money::fromMinorUnits(1234)->toMinorUnits())->toBe(1234);
});

it('keeps decimal rendering exact without floats', function () {
    expect(Money::fromCents(10086)->toDecimal(4))->toBe('100.8600')
        ->and(Money::fromCents(10086)->decimal(3))->toBe('100.860')
        ->and(Money::fromCents(-123)->toDecimal())->toBe('-1.23');
});

it('renders currencies without fraction digits', function () {
    $currency = new class extends AbstractCurrency
    {
        public function __construct()
        {
            parent::__construct('JPY', '¥', '.', ',', 'before', 0);
        }
    };

    expect(Money::fromDecimal('1234', $currency)->toDecimal())->toBe('1234')
        ->and(Money::fromMinorUnits(-1234, $currency)->toDecimal(0))->toBe('-1234')
        ->and(Money::parse('¥ 1,234', $currency)->toMinorUnits())->toBe(1234);
});

it('rejects ambiguous or lossy decimal inputs', function () {
    expect(fn () => Money::fromDecimal('R$ 1.234,56'))->toThrow(InvalidArgumentException::class, 'Invalid decimal money format')
        ->and(fn () => Money::fromDecimal('1.234'))->toThrow(InvalidArgumentException::class, 'Decimal money format exceeds currency fraction digits')
        ->and(fn () => Money::fromDecimal('92233720368547758.08'))->toThrow(InvalidArgumentException::class, 'Money amount exceeds PHP integer range')
        ->and(fn () => Money::fromDecimal('922337203685477580.80'))->toThrow(InvalidArgumentException::class, 'Money amount exceeds PHP integer range')
        ->and(fn () => Money::fromCents(1)->toDecimal(-1))->toThrow(InvalidArgumentException::class, 'Precision must be greater than or equal to zero')
        ->and(fn () => Money::fromCents(123)->toDecimal(1))->toThrow(InvalidArgumentException::class, 'Precision cannot be lower than currency fraction digits');
});

it('parses formatted money using the selected currency locale', function () {
    expect(Money::parse('R$ 1.234,56')->toCents())->toBe(123456)
        ->and(Money::parse('1234,56')->toCents())->toBe(123456)
        ->and(Money::parse('$ 1,234.56', 'USD')->toCents())->toBe(123456)
        ->and(Money::parse('1.234,56 €', 'EUR')->toCents())->toBe(123456)
        ->and(Money::parse('R$ -1.234,56')->toDecimal())->toBe('-1234.56')
        ->and(Money::parse('-R$ 1.234,56')->toDecimal())->toBe('-1234.56')
        ->and(fn () => Money::parse(''))->toThrow(InvalidArgumentException::class, 'Invalid money format');
});

it('rejects malformed localized parsing by default', function () {
    expect(fn () => Money::parse('R$ 1.23.4,56'))->toThrow(InvalidArgumentException::class, 'Invalid money format')
        ->and(fn () => Money::parse('R$ 12.34,56'))->toThrow(InvalidArgumentException::class, 'Invalid money format')
        ->and(fn () => Money::parse('R$ 1.234.56'))->toThrow(InvalidArgumentException::class, 'Invalid money format')
        ->and(fn () => Money::parse('1R$234,56'))->toThrow(InvalidArgumentException::class, 'Invalid money format')
        ->and(fn () => Money::parse('R$ R$ 1.234,56'))->toThrow(InvalidArgumentException::class, 'Invalid money format');
});

it('keeps lenient parsing explicit for legacy inputs', function () {
    expect(Money::parseLenient('R$ 1.23.4,56')->toDecimal())->toBe('1234.56')
        ->and(Money::parseLenient('R$ 1.234,56')->toCents())->toBe(123456)
        ->and(fn () => Money::parseLenient(''))->toThrow(InvalidArgumentException::class, 'Invalid money format');
});

it('parses custom currencies without symbol or grouping separators', function () {
    $currency = new class extends AbstractCurrency
    {
        public function __construct()
        {
            parent::__construct('TST', '', '.', '', 'before', 2);
        }
    };

    expect(Money::parse('1234.56', $currency)->toCents())->toBe(123456)
        ->and(fn () => Money::parse('1234.567', $currency))->toThrow(InvalidArgumentException::class, 'Invalid money format');
});

it('formats currencies without converting through float', function () {
    expect(Money::fromDecimal('100.86')->format())->toBe('R$ 100,86')
        ->and(Money::fromDecimal('56.60', 'USD')->format())->toBe('$ 56.60')
        ->and(Money::fromDecimal('356.78', 'EUR')->format())->toBe('356,78 €')
        ->and(Money::fromDecimal('1000.86')->format(showSymbol: false))->toBe('1.000,86')
        ->and(Money::fromDecimal('1000.86', 'USD')->format(showThousandsSeparator: false))->toBe('$ 1000.86')
        ->and(Money::fromCents(9007199254740993, 'USD')->format())->toBe('$ 90,071,992,547,409.93');
});

it('covers formatter fallbacks for unusual currency definitions', function () {
    $currency = new class extends AbstractCurrency
    {
        public function __construct()
        {
            parent::__construct('XTS', '¤', '.', '', 'middle', 0);
        }
    };

    $formatter = new MoneyFormatter;

    $emptySymbolCurrency = new class extends AbstractCurrency
    {
        public function __construct()
        {
            parent::__construct('NST', '', '.', ',', 'before', 2);
        }
    };

    expect($formatter->format(1234, $currency))->toBe('¤ 1234')
        ->and($formatter->format(123, $emptySymbolCurrency))->toBe('1.23')
        ->and($formatter->format(-1234, $currency, false))->toBe('-1234');
});

it('performs immutable arithmetic only between matching currencies', function () {
    $money = Money::fromDecimal('100.86');
    $increment = Money::fromCents(5600);
    $total = $money->add($increment);

    expect($money->toDecimal())->toBe('100.86')
        ->and($increment->toDecimal())->toBe('56.00')
        ->and($total->toDecimal())->toBe('156.86')
        ->and($total->sub(Money::fromDecimal('0.86'))->toDecimal())->toBe('156.00')
        ->and($total->isDecimal())->toBeTrue();
});

it('compares money values with currency safety', function () {
    $money = Money::fromDecimal('100.86');
    $same = Money::fromCents(10086);
    $lower = Money::fromDecimal('56.60');
    $usd = Money::fromDecimal('100.86', 'USD');

    expect($money->compare($same))->toBe(0)
        ->and($money->equals($same))->toBeTrue()
        ->and($money->greaterThan($lower))->toBeTrue()
        ->and($lower->lessThan($money))->toBeTrue()
        ->and(fn () => $money->add($usd))->toThrow(InvalidArgumentException::class, 'Cannot operate with different currencies: BRL and USD')
        ->and(fn () => $money->compare($usd))->toThrow(InvalidArgumentException::class, 'Cannot operate with different currencies: BRL and USD');
});

it('requires explicit rounding when multiplying or dividing', function () {
    expect(Money::fromCents(1)->mul('0.5', RoundingMode::HalfAwayFromZero)->toCents())->toBe(1)
        ->and(Money::fromCents(1)->mul('0.5', RoundingMode::HalfTowardsZero)->toCents())->toBe(0)
        ->and(Money::fromCents(10)->mul('2.5', RoundingMode::TowardsZero)->toCents())->toBe(25)
        ->and(Money::fromCents(5)->div(2, RoundingMode::HalfAwayFromZero)->toCents())->toBe(3)
        ->and(Money::fromCents(5)->div(2, RoundingMode::HalfTowardsZero)->toCents())->toBe(2)
        ->and(Money::fromCents(2)->div(1, RoundingMode::HalfAwayFromZero)->toCents())->toBe(2);
});

it('supports all rounding modes with signed values', function () {
    expect(Money::fromCents(5)->div(2, RoundingMode::TowardsZero)->toCents())->toBe(2)
        ->and(Money::fromCents(5)->div(2, RoundingMode::AwayFromZero)->toCents())->toBe(3)
        ->and(Money::fromCents(5)->div(2, RoundingMode::PositiveInfinity)->toCents())->toBe(3)
        ->and(Money::fromCents(-5)->div(2, RoundingMode::PositiveInfinity)->toCents())->toBe(-2)
        ->and(Money::fromCents(5)->div(2, RoundingMode::NegativeInfinity)->toCents())->toBe(2)
        ->and(Money::fromCents(-5)->div(2, RoundingMode::NegativeInfinity)->toCents())->toBe(-3)
        ->and(Money::fromCents(5)->div(2, RoundingMode::HalfEven)->toCents())->toBe(2)
        ->and(Money::fromCents(7)->div(2, RoundingMode::HalfEven)->toCents())->toBe(4)
        ->and(Money::fromCents(8)->div(3, RoundingMode::HalfEven)->toCents())->toBe(3)
        ->and(Money::fromCents(7)->div(3, RoundingMode::HalfEven)->toCents())->toBe(2);
});

it('rejects invalid factors and division by zero', function () {
    expect(fn () => Money::fromCents(1)->mul('invalid', RoundingMode::TowardsZero))
        ->toThrow(InvalidArgumentException::class, 'Multiplier must be an integer or decimal string')
        ->and(fn () => Money::fromCents(1)->div('invalid', RoundingMode::TowardsZero))
        ->toThrow(InvalidArgumentException::class, 'Divisor must be an integer or decimal string')
        ->and(fn () => Money::fromCents(1)->div('0', RoundingMode::TowardsZero))
        ->toThrow(InvalidArgumentException::class, 'Division by zero');
});

it('guards internal integer invariants', function () {
    $money = Money::fromCents(1);
    $divideAndRound = new ReflectionMethod($money, 'divideAndRound');
    $normalizeIntegerString = new ReflectionMethod(Money::class, 'normalizeIntegerString');

    expect(fn () => $divideAndRound->invoke($money, '1', '0', RoundingMode::TowardsZero))
        ->toThrow(InvalidArgumentException::class, 'Division by zero')
        ->and(fn () => $normalizeIntegerString->invoke(null, 'invalid'))
        ->toThrow(InvalidArgumentException::class, 'Invalid integer money format');
});

it('registers and creates currencies correctly', function () {
    $factory = new CurrencyFactory;

    expect($factory->create('brl'))->toBeInstanceOf(BRL::class)
        ->and($factory->create('USD'))->toBeInstanceOf(USD::class)
        ->and($factory->create('EUR'))->toBeInstanceOf(EUR::class)
        ->and($factory->getAvailableCurrencies())->toContain('BRL', 'USD', 'EUR')
        ->and(fn () => $factory->create('GBP'))->toThrow(InvalidArgumentException::class, 'Unsupported currency: GBP');
});

it('allows custom currencies to be registered', function () {
    $currency = new class extends AbstractCurrency
    {
        public function __construct()
        {
            parent::__construct('GBP', '£', '.', ',', 'before', 2);
        }
    };

    $factory = new CurrencyFactory;
    $factory->register($currency);
    $created = $factory->create('GBP');

    expect($created)->toBeInstanceOf(CurrencyInterface::class)
        ->and($created->getCode())->toBe('GBP')
        ->and($created->getSymbol())->toBe('£')
        ->and($created->getDecimalSeparator())->toBe('.')
        ->and($created->getThousandsSeparator())->toBe(',')
        ->and($created->getSymbolPosition())->toBe('before')
        ->and($created->getFractionDigits())->toBe(2);
});
