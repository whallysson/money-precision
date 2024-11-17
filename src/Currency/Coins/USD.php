<?php

declare(strict_types=1);

namespace Whallysson\Money\Currency\Coins;

use Whallysson\Money\Currency\AbstractCurrency;

/**
 * Class USD
 *
 * @author Whallysson Avelino <whallysson.dev@gmail.com>
 * @package Whallysson\Money\Currency\Coins
 */
class USD extends AbstractCurrency
{
    public function __construct()
    {
        parent::__construct(
            'USD',
            '$',
            '.',
            ',',
            'before'
        );
    }
}
