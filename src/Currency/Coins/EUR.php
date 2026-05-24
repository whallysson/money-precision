<?php

declare(strict_types=1);

namespace Whallysson\Money\Currency\Coins;

use Whallysson\Money\Currency\AbstractCurrency;

/**
 * Class EUR
 *
 * @author Whallysson Avelino <whallysson.dev@gmail.com>
 */
class EUR extends AbstractCurrency
{
    public function __construct()
    {
        parent::__construct(
            'EUR',
            '€',
            ',',
            '.',
            'after'
        );
    }
}
