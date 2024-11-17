# MoneyPrecision

**MoneyPrecision** is a PHP library designed to handle monetary values with precision, ensuring accurate conversions, formatting, and arithmetic operations. With support for multiple currencies, it is ideal for applications that demand reliability in financial calculations.

---

## 🚀 Features

- Conversions between integer and decimal values ​​while maintaining precision.
- Arithmetic operations (addition, subtraction, multiplication and division) with high accuracy.
- Comparisons between monetary values.
- Support for multiple currencies with custom formatting.
- Intuitive and extensible API.

- **Precise Conversion**:
    - Integer to Decimal and vice-versa.
    - Handles trailing zeros (e.g., `5660 -> 56.60`).

- **Arithmetic Operations**:
    - Addition, subtraction, multiplication, and division.
    - Guaranteed accuracy with `bcmath`.

- **Currency Support**:
    - Easily extendable for multiple currencies.
    - Includes BRL, USD, and EUR by default.

- **Comparison**:
    - Compare monetary values using `equals`, `greaterThan`, and `lessThan`.

- **Customizable Formatting**:
    - Flexible formatting with symbols, separators, and positioning.

---

## 🛠 Installation

Install **MoneyPrecision** with [Composer](https://getcomposer.org/):


```bash
"whallysson/money-precision": "^1.0"
```

or run

```bash
composer require whallysson/money-precision
```

## 📖 Documentation

For detailed information and examples, visit the [documentation](https://github.com/whallysson/money-precision/blob/main/README.md).


## 📚 Usage
### Basic Conversions

```php
<?php

use Whallysson\Money\Money;

echo Money::of(10086)->decimal() . PHP_EOL; // Output: 100.86
echo Money::of(5660)->decimal() . PHP_EOL; // Output: 56.60
echo Money::of(100.86)->int() . PHP_EOL; // Output: 10086
echo Money::of('56.60')->int() . PHP_EOL; // Output: 5660
```

### Arithmetic Operations

```php
<?php

use Whallysson\Money\Money;

$money1 = Money::of(100.86);
$money2 = Money::of(56.60);

echo $money1->add($money2)->decimal() . PHP_EOL; // Output: 157.46
echo $money1->sub($money2)->decimal() . PHP_EOL; // Output: 44.26
echo $money1->mul(2)->decimal() . PHP_EOL; // Output: 201.72
echo $money1->div(2)->decimal() . PHP_EOL; // Output: 50.43
```

### Comparisons

```php
<?php

use Whallysson\Money\Money;

$money1 = Money::of(100.86);
$money2 = Money::of(56.60);

var_dump([
    'equals' => $money1->equals($money2), // false
    'greaterThan' => $money1->greaterThan($money2), // true
    'lessThan' => $money1->lessThan($money2), // false
]);
```

### Currency Formatting

```php
<?php

use Whallysson\Money\Money;

echo Money::of(100.86)->format() . PHP_EOL; // Output: R$ 100,86
echo Money::of(56.60)->format('USD') . PHP_EOL; // Output: $ 56.60
echo Money::of(356.78->format('EUR') . PHP_EOL; // Output: 356,78 €
```





## Contributing

Please see [CONTRIBUTING](https://github.com/whallysson/money-precision/blob/master/CONTRIBUTING.md) for details.

## Support

###### Security: If you discover any security related issues, please email whallysson.dev@gmail.com instead of using the issue tracker.

Se você descobrir algum problema relacionado à segurança, envie um e-mail para whallysson.dev@gmail.com em vez de usar o rastreador de problemas.

Thank you

## Credits

- [Whallysson Avelino](https://github.com/whallysson) (Developer)
- [Whallysson](https://github.com/whallysson) (Team)
- [All Contributors](https://github.com/whallysson/money-precision/contributors) (This Rock)

## 📝 License

The MIT License (MIT). Please see [License File](https://github.com/whallysson/money-precision/blob/master/LICENSE) for more information.