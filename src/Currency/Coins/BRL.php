<?php

declare(strict_types=1);

namespace Whallysson\Money\Currency\Coins;

use Whallysson\Money\Currency\AbstractCurrency;

/**
 * Class BRL
 *
 * @author Whallysson Avelino <whallysson.dev@gmail.com>
 */
class BRL extends AbstractCurrency
{
    public function __construct()
    {
        parent::__construct(
            'BRL',
            'R$',
            ',',
            '.',
            'before'
        );
    }
}
