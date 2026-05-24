# MoneyPrecision

[![Maintainer](http://img.shields.io/badge/maintainer-@whallysson-blue.svg?style=flat-square)](https://www.linkedin.com/in/whallyssonavelino/?locale=en_US)
[![Source Code](http://img.shields.io/badge/source-whallysson/money--precision-blue.svg?style=flat-square)](https://github.com/whallysson/money-precision)
[![PHP from Packagist](https://img.shields.io/packagist/php-v/whallysson/money-precision.svg?style=flat-square)](https://packagist.org/packages/whallysson/money-precision)
[![Latest Version](https://img.shields.io/github/release/whallysson/money-precision.svg?style=flat-square)](https://github.com/whallysson/money-precision/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Total Downloads](https://img.shields.io/packagist/dt/whallysson/money-precision.svg?style=flat-square)](https://packagist.org/packages/whallysson/money-precision)

**MoneyPrecision** is a PHP library for precise monetary values, explicit minor-unit conversion, currency-aware arithmetic, and locale-safe formatting.

## Requirements

- PHP `>=8.4`
- `ext-bcmath`

## Installation

```bash
composer require whallysson/money-precision:^3.0
```

## Core Rules

- Use explicit constructors: `fromCents()`, `fromDecimal()`, and `parse()`.
- Do not pass floats for monetary values.
- Values are stored internally as integer minor units.
- Money objects are immutable: arithmetic returns a new instance.
- Each value carries a currency, and arithmetic between different currencies throws.
- Multiplication and division require an explicit `RoundingMode`.
- Parsing and formatting are separate from calculation.

## Usage

### Explicit Conversion

```php
<?php

use Whallysson\Money\Money;

echo Money::fromCents(5660)->toDecimal() . PHP_EOL; // 56.60
echo Money::fromDecimal('56.60')->toCents() . PHP_EOL; // 5660

echo Money::fromCents(56)->toDecimal() . PHP_EOL; // 0.56
echo Money::fromDecimal('56')->toCents() . PHP_EOL; // 5600
```

### Parsing Formatted Values

```php
<?php

use Whallysson\Money\Money;

echo Money::parse('R$ 1.234,56')->toCents() . PHP_EOL; // 123456
echo Money::parse('$ 1,234.56', 'USD')->toDecimal() . PHP_EOL; // 1234.56
echo Money::parse('1.234,56 €', 'EUR')->toCents() . PHP_EOL; // 123456
```

### Arithmetic

```php
<?php

use Whallysson\Money\Money;

$money = Money::fromDecimal('100.86');
$fee = Money::fromCents(5600);

echo $money->add($fee)->toDecimal() . PHP_EOL; // 156.86
echo $money->sub(Money::fromDecimal('0.86'))->toDecimal() . PHP_EOL; // 100.00
echo $money->toDecimal() . PHP_EOL; // 100.86
```

### Multiplication And Division

```php
<?php

use Whallysson\Money\Money;
use Whallysson\Money\RoundingMode;

$money = Money::fromDecimal('100.86');

echo $money->mul('2', RoundingMode::HalfAwayFromZero)->toDecimal() . PHP_EOL; // 201.72
echo $money->div('2', RoundingMode::HalfAwayFromZero)->toDecimal() . PHP_EOL; // 50.43
```

### Comparisons

```php
<?php

use Whallysson\Money\Money;

$money = Money::fromDecimal('100.86');
$other = Money::fromDecimal('56.60');

var_dump([
    'equals' => $money->equals($other), // false
    'greaterThan' => $money->greaterThan($other), // true
    'lessThan' => $money->lessThan($other), // false
]);
```

### Currency Formatting

```php
<?php

use Whallysson\Money\Money;

echo Money::fromDecimal('100.86')->format() . PHP_EOL; // R$ 100,86
echo Money::fromDecimal('56.60', 'USD')->format() . PHP_EOL; // $ 56.60
echo Money::fromDecimal('356.78', 'EUR')->format() . PHP_EOL; // 356,78 €
```

## Contributing

Please see [CONTRIBUTING](https://github.com/whallysson/money-precision/blob/master/CONTRIBUTING.md) for details.

## Support

Security: If you discover any security related issues, please email whallysson.dev@gmail.com instead of using the issue tracker.

Se você descobrir algum problema relacionado à segurança, envie um e-mail para whallysson.dev@gmail.com em vez de usar o rastreador de problemas.

## Credits

- [Whallysson Avelino](https://github.com/whallysson) (Developer)
- [Whallysson](https://github.com/whallysson) (Team)
- [All Contributors](https://github.com/whallysson/money-precision/contributors)

## License

The MIT License (MIT). Please see [License File](https://github.com/whallysson/money-precision/blob/master/LICENSE) for more information.
