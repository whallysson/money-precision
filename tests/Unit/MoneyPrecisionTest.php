<?php

declare(strict_types=1);

use Whallysson\Money\Currency\Coins\BRL;
use Whallysson\Money\Currency\Coins\EUR;
use Whallysson\Money\Currency\Coins\USD;
use Whallysson\Money\Currency\CurrencyFactory;
use Whallysson\Money\Money;
use Whallysson\Money\Money\MoneyFormatter;

beforeEach(function () {
    $this->formatter = new MoneyFormatter;
    $this->factory = new CurrencyFactory;
});

it('should convert integer to decimal correctly', function () {
    expect(Money::of(10086)->decimal())->toBe('100.86')
        ->and(Money::of(5660)->decimal())->toBe('56.60');
});

it('should convert decimal to integer correctly', function () {
    expect(Money::of(100.86)->int())->toBe(10086)
        ->and(Money::of('56.60')->int())->toBe(5660);
});

it('should handle different decimal precisions', function () {
    expect(Money::of(10086)->decimal(3))->toBe('100.860');
});

it('should perform addition correctly', function () {
    $money1 = Money::of(100.86);
    $money2 = Money::of(56);
    expect($money1->add($money2)->decimal())->toBe('156.86');
});

it('should perform subtraction correctly', function () {
    $money1 = Money::of(100.86);
    $money2 = Money::of(56.60);
    expect($money1->sub($money2)->decimal())->toBe('44.26');
});

it('should perform multiplication correctly', function () {
    $money1 = Money::of(100.86);
    expect($money1->mul(2)->decimal())->toBe('201.72');
});

it('should perform division correctly', function () {
    $money1 = Money::of(100.86);
    expect($money1->div(2)->decimal())->toBe('50.43');
});

it('should format Brazilian Real (BRL) by default', function () {
    $money = Money::of(100.86);
    expect($money->format())->toBe('R$ 100,86');
});

it('should format USD currency', function () {
    $money = Money::of(56.60);
    expect($money->format('USD'))->toBe('$ 56.60');
});

it('should format EUR currency', function () {
    $money = Money::of(356.78);
    expect($money->format('EUR'))->toBe('356,78 €');
});

it('should compare money values correctly', function () {
    $money1 = Money::of(100.86);
    $money2 = Money::of(56.60);

    expect($money1->equals($money2))->toBeFalse()
        ->and($money1->greaterThan($money2))->toBeTrue()
        ->and($money1->lessThan($money2))->toBeFalse();
});

it('should throw exception for invalid operation in add', function () {
    $money1 = Money::of(100.86);
    expect(fn () => $money1->add(''))->toThrow(\InvalidArgumentException::class);
});

it('should throw exception for invalid operation in sub', function () {
    $money1 = Money::of(100.86);
    expect(fn () => $money1->sub(''))->toThrow(\InvalidArgumentException::class);
});

it('should throw exception for invalid operation in mul', function () {
    $money1 = Money::of(100.86);
    expect(fn () => $money1->mul(''))->toThrow(\InvalidArgumentException::class);
});

it('should throw exception for invalid operation in div', function () {
    $money1 = Money::of(100.86);
    expect(fn () => $money1->div('null'))->toThrow(\InvalidArgumentException::class);
});

it('MoneyFormatter should format currencies correctly with symbol and thousand separator', function () {
    $formattedBRL = $this->formatter->format('1000.86', CurrencyFactory::get('BRL'), true, true);
    $formattedUSD = $this->formatter->format('1000.86', CurrencyFactory::get('USD'), true, true);
    $formattedEUR = $this->formatter->format('1000.86', CurrencyFactory::get('EUR'), true, true);

    expect($formattedBRL)->toBe('R$ 1.000,86');
    expect($formattedUSD)->toBe('$ 1,000.86');
    expect($formattedEUR)->toBe('1.000,86 €');
});

it('MoneyFormatter should format currencies correctly without symbol', function () {
    $formattedBRL = $this->formatter->format('1000.86', CurrencyFactory::get('BRL'), false, true);
    $formattedUSD = $this->formatter->format('1000.86', CurrencyFactory::get('USD'), false, true);
    $formattedEUR = $this->formatter->format('1000.86', CurrencyFactory::get('EUR'), false, true);

    expect($formattedBRL)->toBe('1.000,86');
    expect($formattedUSD)->toBe('1,000.86');
    expect($formattedEUR)->toBe('1.000,86');
});

it('MoneyFormatter should format currencies correctly without thousand separator', function () {
    $formattedBRL = $this->formatter->format('1000.86', CurrencyFactory::get('BRL'), true, false);
    $formattedUSD = $this->formatter->format('1000.86', CurrencyFactory::get('USD'), true, false);
    $formattedEUR = $this->formatter->format('1000.86', CurrencyFactory::get('EUR'), true, false);

    expect($formattedBRL)->toBe('R$ 1000,86');
    expect($formattedUSD)->toBe('$ 1000.86');
    expect($formattedEUR)->toBe('1000,86 €');
});

it('MoneyFormatter should format currencies correctly without symbol and thousand separator', function () {
    $formattedBRL = $this->formatter->format('1000.86', CurrencyFactory::get('BRL'), false, false);
    $formattedUSD = $this->formatter->format('1000.86', CurrencyFactory::get('USD'), false, false);
    $formattedEUR = $this->formatter->format('1000.86', CurrencyFactory::get('EUR'), false, false);

    expect($formattedBRL)->toBe('1000,86');
    expect($formattedUSD)->toBe('1000.86');
    expect($formattedEUR)->toBe('1000,86');
});

it('CurrencyFactory should register and create currencies correctly', function () {
    $brl = $this->factory->create('BRL');
    $usd = $this->factory->create('USD');
    $eur = $this->factory->create('EUR');

    expect($brl)->toBeInstanceOf(BRL::class);
    expect($usd)->toBeInstanceOf(USD::class);
    expect($eur)->toBeInstanceOf(EUR::class);

    // Testando exceção para moeda não suportada
    expect(fn () => $this->factory->create('GBP'))->toThrow(\InvalidArgumentException::class, 'Unsupported currency: GBP');
});

it('CurrencyFactory should return available currencies', function () {
    $availableCurrencies = $this->factory->getAvailableCurrencies();

    expect($availableCurrencies)->toBeArray();
    expect($availableCurrencies)->toContain('BRL', 'USD', 'EUR');
});

it('should throw InvalidArgumentException for invalid amount format', function () {
    expect(fn () => Money::of('invalid_amount'))->toThrow(\InvalidArgumentException::class);
});

it('should throw InvalidArgumentException for invalid value type in getValue', function () {
    $money = Money::of(100.86);
    expect(fn () => $money->add(new \stdClass))->toThrow(\InvalidArgumentException::class, 'Invalid value type');
});
