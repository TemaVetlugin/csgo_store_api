<?php

declare(strict_types=1);

namespace App\Repositories\Market;

use App\Entity\DTO\Filter\GetProductsFilter;
use App\Entity\VO\Money;
use App\Services\Market\ApiClients\ProductApiClient;
use App\Services\Market\Provider\CurrencyProvider;
use Illuminate\Cache\Repository as CacheRepository;
use Illuminate\Support\Collection;

class ProductRepository
{
    private const CACHE_KEY_PRODUCTS = 'market.products.all';
    private const CACHE_LIFETIME_PRODUCTS = 86400;

    public function __construct(
        private readonly ProductApiClient $productApiClient,
        private readonly CacheRepository $cacheRepository,
        private readonly CurrencyProvider $currencyProvider,
    ) {
    }

    public function refreshCache(): array
    {
        $products = $this->productApiClient->getProducts();

        foreach ($products as $key => $product) {
            $products[$key]['price'] = new Money($product['price'], $this->currencyProvider->getMarketplaceCurrencyCode());
        }
	logger(['pi' => $products[0]['extra']['type']]);
	
	$products =  array_filter($products,  function (array $product) {
		 $type = $product['extra']['type'] ?? '';
 	//logger(['type'=> $type, 'is_pro' => $type == 'Container']); 
	return ($product['extra']['type'] ?? '')  !== 'Container';});
//logger(['pip' => $pip]);	
//$products = array_filter($products,  fn (array $product) => ($product['extra']['type'] ?? '')  === 'Container');
        $this->cacheRepository->put(self::CACHE_KEY_PRODUCTS, $products, self::CACHE_LIFETIME_PRODUCTS);

        return $products;
    }

    public function getAll(): array
    {
        if (!$this->cacheRepository->has(self::CACHE_KEY_PRODUCTS)) {
            $this->refreshCache();
        }

        return $this->cacheRepository->get(self::CACHE_KEY_PRODUCTS);
    }

    public function getPopular(int $countToTake): array
    {
        $popularProducts = [];

        $marketProductGroups = (new Collection($this->getAll()))
            ->groupBy('name')
            ->sortBy(
                callback: static fn (Collection $productsByNameGroup) => $productsByNameGroup->count(),
                descending: true
            )
        ;

        foreach ($marketProductGroups as $marketProductGroup) {
            $popularProducts[] = $marketProductGroup
                ->sortBy(static fn (array $product) => $product['price']->getAmount())
                ->first()
            ;

            if (count($popularProducts) === $countToTake) {
                break;
            }
        }

        return $popularProducts;
    }

    public function getByFilter(GetProductsFilter $filter): array
    {
        $marketProducts = $this->getAll();
        $selectedProducts = [];

        $countProductsToSkip = $filter->getPerPage() * ($filter->getPage() - 1);
        $countProductsToTake = $filter->getPerPage() * $filter->getPage();

        foreach ($marketProducts as $product) {
            if (!$this->isProductPassesFilter($product, $filter)) {
                continue;
            }

            $selectedProducts[] = $product;
            if (count($selectedProducts) >= $countProductsToTake) {
                break;
            }
        }

        return array_slice($selectedProducts, $countProductsToSkip, $filter->getPerPage());
    }

    private function isProductPassesFilter(array $product, GetProductsFilter $filter): bool
    {
        if ($filter->getName() !== null && !$this->isNameFilterPassed($product, $filter->getName())) {
            return false;
        }

        if ($filter->getPriceMin() !== null && !$this->isPriceMinFilterPassed($product, $filter->getPriceMin())) {
            return false;
        }

        if ($filter->getPriceMax() !== null && !$this->isPriceMaxFilterPassed($product, $filter->getPriceMax())) {
            return false;
        }

        if (count($filter->getIds()) > 0 && !$this->isIdsFilterPassed($product, $filter->getIds())) {
            return false;
        }

        if (count($filter->getTypes()) > 0 && !$this->isTypesFilterPassed($product, $filter->getTypes())) {
            return false;
        }

        if (count($filter->getWeapons()) > 0 && !$this->isWeaponsFilterPassed($product, $filter->getWeapons())) {
            return false;
        }

        if (count($filter->getExteriors()) > 0 && !$this->isExteriorsFilterPassed($product, $filter->getExteriors())) {
            return false;
        }

        if (count($filter->getQualities()) > 0 && !$this->isQualitiesFilterPassed($product, $filter->getQualities())) {
            return false;
        }

        if (count($filter->getRarities()) > 0 && !$this->isRaritiesFilterPassed($product, $filter->getRarities())) {
            return false;
        }

        if (count($filter->getStickerNames()) > 0 && !$this->isStickerNamesFilterPassed($product, $filter->getStickerNames())) {
            return false;
        }

        return true;
    }

    private function isNameFilterPassed(array $product, string $nameFilter): bool
    {
        return isset($product['name']) && str_contains($product['name'], $nameFilter);
    }

    private function isPriceMinFilterPassed(array $product, float $priceMinFilter): bool
    {
        return $product['price']->getAmount() >= $priceMinFilter;
    }

    private function isPriceMaxFilterPassed(array $product, float $priceMaxFilter): bool
    {
        return $product['price']->getAmount() <= $priceMaxFilter;
    }

    private function isIdsFilterPassed(array $product, array $idsFilter): bool
    {
        return isset($product['id']) && in_array($product['id'], $idsFilter, true);
    }

    private function isTypesFilterPassed(array $product, array $typesFilter): bool
    {
        return isset($product['extra']['type']) && in_array($product['extra']['type'], $typesFilter, true);
    }

    private function isWeaponsFilterPassed(array $product, array $weaponsFilter): bool
    {
        return isset($product['extra']['weapon']) && in_array($product['extra']['weapon'], $weaponsFilter, true);
    }

    private function isExteriorsFilterPassed(array $product, array $exteriorsFilter): bool
    {
        return isset($product['extra']['exterior']) && in_array($product['extra']['exterior'], $exteriorsFilter, true);
    }

    private function isQualitiesFilterPassed(array $product, array $qualitiesFilter): bool
    {
        return isset($product['extra']['quality']) && in_array($product['extra']['quality'], $qualitiesFilter, true);
    }

    private function isRaritiesFilterPassed(array $product, array $raritiesFilter): bool
    {
        return isset($product['extra']['rarity']) && in_array($product['extra']['rarity'], $raritiesFilter, true);
    }

    private function isStickerNamesFilterPassed(array $product, array $stickerNamesFilter): bool
    {
        if (!isset($product['stickers']) || !is_array($product['stickers'])) {
            return false;
        }

        $missedNames = array_diff($stickerNamesFilter, array_column($product['stickers'], 'name'));

        return count($missedNames) === 0;
    }
}
