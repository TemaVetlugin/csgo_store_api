<?php

declare(strict_types=1);

namespace App\Services\Market\Manager;

use App\Repositories\Market\CurrencyRepository;
use App\Repositories\Market\ProductRepository;

class MarketCacheManager
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly CurrencyRepository $currencyRepository,
    ) {
    }

    public function refreshCache(): void
    {
        $this->productRepository->refreshCache();
        $this->currencyRepository->refreshCache();
    }
}
