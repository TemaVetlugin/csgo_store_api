<?php

declare(strict_types=1);

namespace App\Services\Transaction;

use App\Models\Enum\TransactionStatus;
use InvalidArgumentException;

class TransactionStatusMapper
{
    public function mapFromMarketOfferStatus(string $marketApiStatus): TransactionStatus
    {
        return match ($marketApiStatus) {
            'accepted' => TransactionStatus::Accepted,
            'pending' => TransactionStatus::Pending,
            'creating_trade' => TransactionStatus::CreatingTrade,
            'waiting_accept' => TransactionStatus::WaitingAccept,
            'canceled' => TransactionStatus::Canceled,
            'timeout' => TransactionStatus::Timeout,
            'invalid_trade_token' => TransactionStatus::InvalidTradeToken,
            'user_not_tradable' => TransactionStatus::UserNotTradable,
            'trade_create_error' => TransactionStatus::TradeCreateError,

            default => throw new InvalidArgumentException(
                sprintf('Cannot map offer status "%s".', $marketApiStatus)
            ),
        };
    }

    public function mapFromMarketErrorCode(int $apiErrorCode): TransactionStatus
    {
        return match ($apiErrorCode) {
            -6 => TransactionStatus::MarketDisabled,
            -7 => TransactionStatus::MarketIsTemporaryUnavailable,
            4 => TransactionStatus::InsufficientFunds,
            5 => TransactionStatus::ProductUnavailable,
            6 => TransactionStatus::ProductNotFoundAtSpecifiedPrice,
            8 => TransactionStatus::InvalidTradeLinkPartner,

            default => TransactionStatus::UnknownError,
        };
    }

    public function getStatusDescription(array $transactions): array
    {
        $trade = 'Pending';
        $description = '';

        // Считаем количество каждого статуса
        $statusCounts = [];
        foreach ($transactions as $transaction) {
            $status = $transaction['status']->value;
            if (!isset($statusCounts[$status])) {
                $statusCounts[$status] = 0;
            }
            $statusCounts[$status]++;
        }

        $uniqueStatuses = array_keys($statusCounts);
        $allSameStatus = count($uniqueStatuses) === 1;

        if ($allSameStatus) {
            $status = $uniqueStatuses[0];
            $trade = $this->getTradeName($status);
            $description = $this->getStatusDescriptionText($status);
        } else {
            // Транзакции имеют разные статусы
            $hasAccepted = isset($statusCounts[TransactionStatus::Accepted->value]);

            if ($hasAccepted) {
                $trade = 'Accepted';
                $negativeStatusTransactions = [];

                foreach ($transactions as $transaction) {
                    if ($transaction['status']->value !== TransactionStatus::Accepted->value) {
                        $negativeStatusTransactions[] = [
                            'name' => $transaction['product_name'],
                            'status' => $transaction['status']->name,
                        ];
                    }
                }

                if (!empty($negativeStatusTransactions)) {
                    $negativeDescriptions = [];
                    foreach ($negativeStatusTransactions as $transaction) {
                        $negativeDescriptions[] = "{$transaction['name']} - {$transaction['status']}";
                    }
                    $description = "Not all transactions were successful! The following transactions have issues: "
                        . implode(', ', $negativeDescriptions)
                        . ". Please contact support for further assistance.";
                } else {
                    $description = 'Trade is completed successfully.';
                }
            } else {
                // Если Accepted отсутствует, выбираем статус, который встречается чаще всего
                $mostFrequentStatus = array_keys($statusCounts, max($statusCounts))[0];
                $trade = $this->getTradeName($mostFrequentStatus);
                $description = $this->getStatusDescriptionText($mostFrequentStatus);
            }
        }

        return [
            'trade' => $trade,
            'description' => $description,
        ];
    }

    /**
     * Возвращает название статуса для trade.
     */
    private function getTradeName(string $status): string
    {
        return match ($status) {
            TransactionStatus::Accepted->value => 'Accepted',
            TransactionStatus::WaitingAccept->value => 'Waiting Accept',
            TransactionStatus::Canceled->value => 'Canceled',
            TransactionStatus::Timeout->value => 'Timeout',
            TransactionStatus::InvalidTradeToken->value => 'Invalid Trade Token',
            TransactionStatus::UserNotTradable->value => 'User Not Tradable',
            TransactionStatus::TradeCreateError->value => 'Trade Create Error',
            TransactionStatus::ProductUnavailable->value => 'Skin is unavailable',
            default => 'Pending',
        };
    }

    /**
     * Возвращает описание для статуса.
     */
    private function getStatusDescriptionText(string $status): string
    {
        return match ($status) {
            TransactionStatus::Accepted->value => 'Trade is completed successfully.',
            TransactionStatus::WaitingAccept->value => 'Your trade is now pending in Steam. To complete your purchase, please log into your Steam account and accept the trade offer.',
            TransactionStatus::Canceled->value => 'Trade has been canceled. Please contact support for further assistance.',
            TransactionStatus::Timeout->value => 'The trade offer has expired. Please contact support for further assistance.',
            TransactionStatus::InvalidTradeToken->value => 'Invalid trade token detected. Please contact support for further assistance.',
            TransactionStatus::UserNotTradable->value => 'You have trade restrictions. Please contact support for further assistance.',
            TransactionStatus::TradeCreateError->value => 'An error occurred while creating the trade. Please contact support for further assistance.',
            TransactionStatus::ProductUnavailable->value => 'Skin is out of stock in the store. Please contact support for further assistance.',
            default => 'Your trade is being processed. Please wait.',
        };
    }
}
