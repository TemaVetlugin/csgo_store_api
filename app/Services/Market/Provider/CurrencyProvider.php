<?php

declare(strict_types=1);

namespace App\Services\Market\Provider;

use App\Repositories\Market\CurrencyRepository;

class CurrencyProvider
{
    public function __construct(
        private readonly CurrencyRepository $currencyRepository,
        private readonly string $defaultClientCurrencyCode,
        private readonly string $marketplaceCurrencyCode,
    ) {
    }

    public function getMarketplaceCurrencyCode(): string
    {
        return $this->marketplaceCurrencyCode;
    }

    public function getClientCurrencyCode(): string
    {
        return $this->defaultClientCurrencyCode;
    }

    public function getByCode(string $currencyCode): array
    {
        return $this->currencyRepository->getByCurrencyCode($currencyCode);
    }
}
