<?php

declare(strict_types=1);

namespace App\Repositories\Market;

use App\Services\Market\ApiClients\CurrencyApiClient;
use Illuminate\Cache\Repository as CacheRepository;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class CurrencyRepository
{
    private const CACHE_KEY_CURRENCIES = 'market.currencies.all';
    private const CACHE_LIFETIME_CURRENCIES = 86400;

    public function __construct(
        private readonly CurrencyApiClient $currencyApiClient,
        private readonly CacheRepository $cacheRepository,
    ) {
    }

    public function refreshCache(): array
    {
        $currencies = $this->currencyApiClient->getCurrencies();

        $this->cacheRepository->put(self::CACHE_KEY_CURRENCIES, $currencies, self::CACHE_LIFETIME_CURRENCIES);

        return $currencies;
    }

    public function getAll(): array
    {
        if (!$this->cacheRepository->has(self::CACHE_KEY_CURRENCIES)) {
            $this->refreshCache();
        }

        return $this->cacheRepository->get(self::CACHE_KEY_CURRENCIES);
    }

    public function getByCurrencyCode(string $currencyCode): array
    {
        $currencies = new Collection($this->getAll());

        $currency = $currencies->first(fn (array $currency) => $currency['code'] === $currencyCode);

        if ($currency === null) {
            throw new InvalidArgumentException(sprintf('Currency cannot be resolved by code "%s".', $currencyCode));
        }

        return $currency;
    }
}
