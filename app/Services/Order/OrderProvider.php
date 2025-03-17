<?php

declare(strict_types=1);

namespace App\Services\Order;

use App\Entity\VO\Money;
use App\Models\Enum\OrderStatus;
use App\Models\Order;
use App\Repositories\Order\OrderRepository;
use App\Services\Currency\CurrencyConverter;
use App\Services\Market\Provider\CurrencyProvider;
use App\Services\Market\Provider\ProductProvider;

class OrderProvider
{
    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly CurrencyProvider $currencyProvider,
        private readonly CurrencyConverter $currencyConverter,
        private readonly ProductProvider   $productProvider,
    ) {
    }

    public function findByUuid(string $uuid): Order|null
    {
        return $this->orderRepository->findByUuid($uuid);
    }

    public function updateOrderData(array $data, Order $order): Order
    {
        $order
        ->setFirstName($data['firstName'])
        ->setLastName($data['lastName'])
        ->setCountry($data['country'])
        ->setCountryIso($data['countryIso'])
        ->setCity($data['city'])
        ->setAddress($data['address'])
        ->setPhone($data['phone'])
        ->setNote($data['notes']);
        return $order;
    }

    public function getPendingOrdersTotalAmount(): Money
    {
        $pendingOrders = $this->orderRepository->getByStatuses([ OrderStatus::Pending]);

        $totalAmountInMarketCurrency = 0;

        foreach ($pendingOrders as $order) {
            $totalAmountInMarketCurrency += $this->convertOrderTotalPriceToMarketCurrency($order);
        }

        return new Money($totalAmountInMarketCurrency, $this->currencyProvider->getMarketplaceCurrencyCode());
    }

    public function getOrderDetails($uuid): array
    {
        $order = $this->findByUuid($uuid);
        $transactions = $order->getTransactions();
        $ids = [];
        $prices = [];
        foreach($transactions as $transaction){
            $ids[] = $transaction['product_id'];
            $prices[$transaction['product_id']] = $transaction['payed_amount'];
        }

        $products = $this->productProvider->getByIds($ids);

        foreach($products as &$product){
            $product['price'] = $prices[$product['id']];
        }

        return [
            'products' => $products,
            'price' => $order->getTotalPrice(),
            ];
    }

    private function convertOrderTotalPriceToMarketCurrency(Order $order): float
    {
        $marketCurrencyCode = strtolower($this->currencyProvider->getMarketplaceCurrencyCode());
        $orderCurrency = strtolower($order->getTotalPriceCurrency());

        if ($orderCurrency === $marketCurrencyCode) {
            return $order->getTotalPrice();
        }

        $orderAmount = $this->currencyConverter->convert(
            (string) $order->getTotalPrice(),
            $orderCurrency,
            $marketCurrencyCode
        );

        return (float) $this->currencyConverter->bankingRound($orderAmount);
    }
}
