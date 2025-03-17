<?php

declare(strict_types=1);

namespace App\Exceptions\Market\BuyProduct\Case;

enum BuyProductExceptionCase: string
{
    case ProductsAreOutOfStock = 'products_are_out_of_stock';
    case InsufficientFundsOnMarketAccount = 'order_amount_is_too_large';
    case PaymentInitialisationFailed = 'payment_initialisation_failed';
    case PaymentCheckStatusFailed = 'payment_check_status_failed';
    case OrderTooSmallFailed = 'order_amount_is_too_small';

    public function toString(): string
    {
        return $this->value;
    }
}
