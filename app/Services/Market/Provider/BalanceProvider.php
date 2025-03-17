<?php

declare(strict_types=1);

namespace App\Services\Market\Provider;

use App\Entity\VO\Money;
use App\Services\Market\ApiClients\AccountApiClient;

class BalanceProvider
{
    public function __construct(private readonly AccountApiClient $accountApiClient)
    {
    }

    public function getBalance(string $currencyCode): Money
    {
        $currencyCode = strtolower($currencyCode);

        $balanceInCurrencies = $this->accountApiClient->getBalance();

        return new Money((float) $balanceInCurrencies[$currencyCode], $currencyCode);
    }
}
