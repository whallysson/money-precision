<?php

declare(strict_types=1);

use Whallysson\Money\Money;

require_once __DIR__ . '/../vendor/autoload.php';

// Conversões básicas
echo Money::of(10086)->decimal() . PHP_EOL; // Output: 100.86
echo Money::of(5660)->decimal() . PHP_EOL; // Output: 56.60
echo Money::of(100.86)->int() . PHP_EOL; // Output: 10086
echo Money::of('56.60')->int() . PHP_EOL; // Output: 5660

// Usando precisão diferente
echo Money::of(10086)->decimal(3) . PHP_EOL; // Output: 100.860

// Operações aritméticas precisas
$money1 = Money::of(100.86);
$money2 = Money::of(56.60);

echo $money1->add($money2)->decimal() . PHP_EOL;    // Output: 157.46
echo $money1->sub($money2)->decimal() . PHP_EOL;    // Output: 44.26
echo $money1->mul(2)->decimal() . PHP_EOL;    // Output: 201.72
echo $money1->div(2)->decimal() . PHP_EOL;    // Output: 50.43

// Currency Formatting
$money1 = Money::of(100.86);
$money2 = Money::of(56.60);
$money3 = Money::of(356.78);

echo $money1->format() . PHP_EOL; // Output: R$ 100,86
echo $money2->format('USD') . PHP_EOL; // Output: $ 56.60
echo $money3->format('EUR') . PHP_EOL; // Output: 356,78 €

// Comparações
var_dump([
    'equals' => $money1->equals($money2), // false
    'greaterThan' => $money1->greaterThan($money2), // true
    'lessThan' => $money1->lessThan($money2), // false
]);

// Formatação
echo Money::of(10086)->format() . PHP_EOL; // Output: R$ 100,86
