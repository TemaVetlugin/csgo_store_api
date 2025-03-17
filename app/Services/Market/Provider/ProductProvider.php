<?php

declare(strict_types=1);

namespace App\Services\Market\Provider;

use App\Entity\DTO\Filter\GetProductsFilter;
use App\Entity\VO\Money;
use App\Repositories\Market\ProductRepository;
use App\Services\Currency\CurrencyConverter;

class ProductProvider
{
    public const PER_PAGE_POPULAR_PRODUCTS = 9;

    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly CurrencyProvider $currencyProvider,
        private readonly CurrencyConverter $currencyConverter,
    ) {
    }

    public function getByIds(array $productIds): array
    {
        $productFilter = (new GetProductsFilter())->setIds($productIds);

        return $this->productRepository->getByFilter($productFilter);
    }

    public function getPopular(): array
    {
        $products = $this->productRepository->getPopular(self::PER_PAGE_POPULAR_PRODUCTS);

        return $this->convertPricesToClientCurrency($products);
    }

    public function getByFilter(GetProductsFilter $filter): array
    {
        $filter = $this->convertPriceFilterToMarketplaceCurrency($filter);
        $products = $this->productRepository->getByFilter($filter);

        return $this->convertPricesToClientCurrency($products);
    }

    private function convertPricesToClientCurrency(array $products): array
    {
        $clientCurrency = $this->currencyProvider->getClientCurrencyCode();
        $marketplaceCurrency = $this->currencyProvider->getMarketplaceCurrencyCode();

        foreach ($products as $index => $product) {
            $productPrice = (string) $product['price']->getAmount();

            $convertedPrice = $this->currencyConverter->convert($productPrice, $marketplaceCurrency, $clientCurrency);
            $convertedRoundedPrice = (float) $this->currencyConverter->bankingRound($convertedPrice);

            $products[$index]['price'] = new Money($convertedRoundedPrice, $clientCurrency);
        }

        return $products;
    }

    private function convertPriceFilterToMarketplaceCurrency(GetProductsFilter $filter): GetProductsFilter
    {
        $convertedFilter = clone $filter;

        $marketplaceCurrency = $this->currencyProvider->getMarketplaceCurrencyCode();
        $clientCurrency = $this->currencyProvider->getClientCurrencyCode();

        if ($marketplaceCurrency === $clientCurrency) {
            return $convertedFilter;
        }

        if ($filter->getPriceMin() !== null) {
            $filterValue = (string) $filter->getPriceMin();
            $convertedValue = $this->currencyConverter->convert($filterValue, $clientCurrency, $marketplaceCurrency);

            $convertedFilter->setPriceMin((float) $convertedValue);
        }

        if ($filter->getPriceMax() !== null) {
            $filterValue = (string) $filter->getPriceMax();
            $convertedValue = $this->currencyConverter->convert($filterValue, $clientCurrency, $marketplaceCurrency);

            $convertedFilter->setPriceMax((float) $convertedValue);
        }

        return $convertedFilter;
    }
}
