<?php

declare(strict_types=1);

namespace App\Services\Market;

use App\Entity\DTO\Market\BuyProductApiPayload;
use App\Exceptions\Market\MarketApiException;
use App\Jobs\SendFailedTransactionMailJob;
use App\Jobs\SendInsufficientFundsOnMarketplaceAccountMailJob;
use App\Models\Enum\TransactionStatus;
use App\Models\Order;
use App\Models\Transaction;
use App\Services\Market\ApiClients\ProductApiClient;
use App\Services\Steam\SteamTradeLinkParser;
use App\Services\Transaction\TransactionStatusMapper;
use Illuminate\Log\Logger;
use Throwable;

class ProductBuyer
{
    public function __construct(
        private readonly SteamTradeLinkParser $steamTradeLinkParser,
        private readonly TransactionStatusMapper $transactionStatusMapper,
        private readonly ProductApiClient $productApiClient,
        private readonly Logger $logger,
    ) {
    }

    public function processOrder(Order $order): void
    {
        foreach ($order->getTransactions() as $transaction) {
            $this->performTransaction($transaction);

            $transaction->save();
        }
    }

    private function performTransaction(Transaction $transaction): void
    {
        $apiRequestPayload = $this->makeBuyProductApiPayloadFromTransaction($transaction);

        try {
            $purchase = $this->productApiClient->buyProduct($apiRequestPayload);

        } catch (MarketApiException $exception) {
            $this->handleMarketApiException($exception, $transaction);

        } catch (Throwable $exception) {
            $this->handleUnexpectedException($exception, $transaction);
        }

        $transaction->setStatus($this->transactionStatusMapper->mapFromMarketOfferStatus($purchase['offer_status']));
        $transaction->setBuyId((int) $purchase['buy_id']);
        $transaction->setProductOriginalPrice((float) $purchase['balance_debited_sum']);
    }

    private function makeBuyProductApiPayloadFromTransaction(Transaction $transaction): BuyProductApiPayload
    {
        $user = $transaction->getOrder()->getUser();

        $userSteamTradeLink = $this->steamTradeLinkParser->parsePayload($user->getSteamTradeLink());

        return new BuyProductApiPayload(
            productId: $transaction->getProductId(),
            tradeLinkPartner: $userSteamTradeLink->getPartner(),
            tradeLinkToken: $userSteamTradeLink->getToken(),
            maxPriceToBuy: $transaction->getProductOriginalPrice(),
            customTransactionId: $transaction->getUuid(),
        );
    }

    private function handleMarketApiException(MarketApiException $marketApiException, Transaction $transaction): void
    {
        if ($marketApiException->getApiErrorCode() !== null) {
            $transactionStatus = $this->transactionStatusMapper->mapFromMarketErrorCode($marketApiException->getApiErrorCode());
            $transaction->setStatus($transactionStatus);
        } else {
            $transaction->setStatus(TransactionStatus::UnknownError);
        }

        $transaction->save();

        if ($transaction->getStatus() === TransactionStatus::InsufficientFunds) {
            SendInsufficientFundsOnMarketplaceAccountMailJob::dispatch(
                $transaction->getOrder()->getUser()->getEmail(),
                [$transaction->getProductName()],
                $transaction->getProductOriginalPrice(),
            );
        }

        SendFailedTransactionMailJob::dispatch($transaction->getId());

        $this->logger->error(
            'Buy Product transaction failed with a market api exception!',
            [
                'api_error_code' => $marketApiException->getApiErrorCode(),
                'api_response' => $marketApiException->getApiResponse(),
                'transaction' => [
                    'uuid' => $transaction->getUuid(),
                    'product_id' => $transaction->getProductId(),
                    'product_name' => $transaction->getProductName(),
                    'product_price' => $transaction->getProductOriginalPrice(),
                ],
            ]
        );
    }

    private function handleUnexpectedException(Throwable $exception, Transaction $transaction): void
    {
        $transaction->setStatus(TransactionStatus::UnknownError);
        $transaction->save();

        SendFailedTransactionMailJob::dispatch($transaction->getId());

        $this->logger->critical(
            'Buy Product transaction failed with an UNEXPECTED exception!',
            [
                'error' => [
                    'message' => $exception->getMessage(),
                    'trace' => $exception->getTraceAsString(),
                    'transaction' => [
                        'uuid' => $transaction->getUuid(),
                        'product_id' => $transaction->getProductId(),
                        'product_name' => $transaction->getProductName(),
                        'product_price' => $transaction->getProductOriginalPrice(),
                    ],
                ],
            ]
        );
    }
}
