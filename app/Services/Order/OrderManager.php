<?php

declare(strict_types=1);

namespace App\Services\Order;

use App\Entity\VO\Money;
use App\Models\Enum\OrderStatus;
use App\Models\Enum\PaymentStatus;
use App\Models\Enum\TransactionStatus;
use App\Models\Enum\TransactionType;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
use App\Services\Currency\CurrencyConverter;
use App\Services\Market\Provider\CurrencyProvider;
use App\Services\Market\Provider\ProductProvider;

class OrderManager
{
    public function __construct(
        private readonly ProductProvider   $productProvider,
        private readonly CurrencyProvider  $currencyProvider,
        private readonly CurrencyConverter $currencyConverter,
    )
    {
    }

    public function createOrder(array $productIds, User $user): Order
    {
        $products = $this->productProvider->getByIds($productIds);

        $totalMarketPrice = $this->countProductsTotalMarketPrice($products);
        $totalMarketPriceConvert = $this->currencyConverter->convert(((string)$totalMarketPrice->getAmount()), $this->currencyProvider->getMarketplaceCurrencyCode(), $this->currencyProvider->getClientCurrencyCode());
        $totalMarketPriceRound = (float)$this->currencyConverter->bankingRound($totalMarketPriceConvert);

        $order = (new Order())
            ->setUuid(uuid_create())
            ->setUser($user)
            ->setStatus(OrderStatus::Created)
            ->setTotalPrice($totalMarketPriceRound)
            ->setTotalPriceCurrency($this->currencyProvider->getClientCurrencyCode());

        $order->save();

        foreach ($products as $product) {
            $this->addTransactionToOrder($product, $order);
        }

        return $order;
    }

    public function updateOrderStatusByPayment(Order $order, PaymentStatus $paymentStatus): void
    {
        switch ($paymentStatus) {
            case PaymentStatus::Created:
                $order->setStatus(OrderStatus::Created);
                break;

            case PaymentStatus::ProcessPending:
            case PaymentStatus::RefundPending:
                $order->setStatus(OrderStatus::Pending);
                break;

            case PaymentStatus::Processed:
            case PaymentStatus::Refunded:
            case PaymentStatus::ChargedBack:
            case PaymentStatus::PartiallyRefunded:
            case PaymentStatus::PartiallyChargedBack:
                $order->setStatus(OrderStatus::Processed);
                break;

            default:
                $order->setStatus(OrderStatus::Failed);
        }
    }

    private function addTransactionToOrder(array $product, Order $order): void
    {
        $productPrice = $product['price'];
        $payedPrice = $this->convertPriceToClientCurrency($productPrice);

        $transaction = (new Transaction())
            ->setStatus(TransactionStatus::Pending)
            ->setUuid(uuid_create())
            ->setOrder($order)
            ->setType(TransactionType::Buy)
            ->setProductId((string)$product['id'])
            ->setProductName((string)$product['name'])
            ->setProductOriginalPrice($productPrice->getAmount())
            ->setPayedAmount($payedPrice->getAmount())
            ->setPayedInCurrency($payedPrice->getCurrencyCode());

        $transaction->save();
    }

    private function countProductsTotalMarketPrice(array $products): Money
    {
        $productsTotalPrice = (float)array_sum(
            array_map(static fn(array $product) => $product['price']->getAmount(), $products)
        );

        $marketCurrencyCode = $this->currencyProvider->getClientCurrencyCode();
//        $marketCurrencyCode = $this->currencyProvider->getMarketplaceCurrencyCode();

        return new Money($productsTotalPrice, $marketCurrencyCode);
    }

    private function convertPriceToClientCurrency(Money $productMarketPrice): Money
    {
        $clientCurrencyCode = $this->currencyProvider->getClientCurrencyCode();

        $convertedPrice = $this->currencyConverter->convert(
            (string)$productMarketPrice->getAmount(),
            $productMarketPrice->getCurrencyCode(),
            $clientCurrencyCode,
        );

        $roundedPrice = (float)$this->currencyConverter->bankingRound($convertedPrice);

        return new Money($roundedPrice, $clientCurrencyCode);
    }
}
