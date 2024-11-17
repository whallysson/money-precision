<?php

declare(strict_types=1);

namespace Whallysson\Money\Currency;

use Whallysson\Money\Currency\Coins\BRL;
use Whallysson\Money\Currency\Coins\EUR;
use Whallysson\Money\Currency\Coins\USD;

/**
 * Class CurrencyFactory
 *
 * @author Whallysson Avelino <whallysson.dev@gmail.com>
 * @package Whallysson\Money\Currency
 */
class CurrencyFactory
{
    /** @var CurrencyInterface[] */
    private array $currencies = [];

    public function __construct()
    {
        $this->register(new BRL());
        $this->register(new USD());
        $this->register(new EUR());
    }

    /**
     * @param CurrencyInterface $currency
     * @return void
     */
    public function register(CurrencyInterface $currency): void
    {
        $this->currencies[$currency->getCode()] = $currency;
    }

    /**
     * @param string $currency
     * @return CurrencyInterface
     */
    public static function get(string $currency): CurrencyInterface
    {
        return (new self())->create($currency);
    }

    /**
     * @param string $code
     * @return CurrencyInterface
     */
    public function create(string $code): CurrencyInterface
    {
        $code = strtoupper($code);

        if (!isset($this->currencies[$code])) {
            throw new \InvalidArgumentException('Unsupported currency: ' . $code);
        }

        return $this->currencies[$code];
    }

    /**
     * @return array
     */
    public function getAvailableCurrencies(): array
    {
        return array_keys($this->currencies);
    }
}
