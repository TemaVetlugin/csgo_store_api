<?php

declare(strict_types=1);

namespace App\Services\Transaction;

use App\Models\Enum\TransactionStatus;
use InvalidArgumentException;

class TransactionStatusReasonProvider
{
    public function getForStatus(TransactionStatus $transactionStatus): string
    {
        return match ($transactionStatus) {
            TransactionStatus::Canceled => 'You rejected trade offer created to provide you with the item.',
            TransactionStatus::Timeout => 'You had not accepted the trade offer created to provide you with the item.',
            TransactionStatus::UserNotTradable => 'Steam User is not tradable, please ensure that your Steam profile can accept trade offers.',
            TransactionStatus::InsufficientFunds => 'The price of the product is too high to process.',
            TransactionStatus::ProductUnavailable => 'The product is no longer available for purchase.',
            TransactionStatus::ProductNotFoundAtSpecifiedPrice => 'The product is not found at specified price.',
            TransactionStatus::InvalidTradeToken, TransactionStatus::InvalidTradeLinkPartner => 'The Steam Trade Link specified in your profile is invalid.',
            TransactionStatus::TradeCreateError, TransactionStatus::UnknownError => 'Trade offer creation is failed due to unknown error.',
            TransactionStatus::MarketDisabled, TransactionStatus::MarketIsTemporaryUnavailable => 'The market is temporarily off and cannot accept buy orders.',

            default => throw new InvalidArgumentException(
                sprintf('Unknown transaction status - "%s"', $transactionStatus->toString())
            ),
        };
    }
}
