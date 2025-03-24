<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\SendFailedTransactionMailJob;
use App\Jobs\SendSuccessTransactionMailJob;
use App\Jobs\SendTransactionAdminMailJob;
use App\Models\Enum\TransactionStatus;
use App\Models\Transaction;
use App\Repositories\User\TransactionRepository;
use App\Services\Transaction\TransactionStatusMapper;
use Illuminate\Config\Repository as ConfigRepository;
use Illuminate\Console\Command;
use Illuminate\Log\Logger;
use WebSocket\Client as WebSocketClient;
use WebSocket\ConnectionException;
use WebSocket\Message\Message;

class MarketWebSocketListenerCommand extends Command
{
    private const BASE_URL_MARKET_WEB_SOCKET_HOST = 'wss://skinsback.com/ws/';
    private const EVENT_NAME_STATUS_CHANGE = 'status_change';
    private const TRANSACTION_STATUSES_TO_NOTIFY_USERS_ABOUT_SUCCESS_TRANSACTIONS = [
        TransactionStatus::Accepted,
    ];
    private const TRANSACTION_STATUSES_TO_NOTIFY_USERS_ABOUT_FAILED_TRANSACTIONS = [
        TransactionStatus::Canceled,
        TransactionStatus::Timeout,
        TransactionStatus::UserNotTradable,
        TransactionStatus::InsufficientFunds,
        TransactionStatus::ProductUnavailable,
        TransactionStatus::ProductNotFoundAtSpecifiedPrice,
        TransactionStatus::InvalidTradeToken,
        TransactionStatus::TradeCreateError,
        TransactionStatus::MarketDisabled,
        TransactionStatus::InvalidTradeToken,
        TransactionStatus::InvalidTradeLinkPartner,
        TransactionStatus::TradeCreateError,
        TransactionStatus::UnknownError,
        TransactionStatus::MarketDisabled,
        TransactionStatus::MarketIsTemporaryUnavailable,
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'market:web-socket-listener';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Listen for the Market events via the Web Socket protocol. More: https://skinsback.com/docs/market_websocket';

    /**
     * Execute the console command.
     */
    public function handle(
        ConfigRepository $configRepository,
        TransactionStatusMapper $transactionStatusMapper,
        TransactionRepository $transactionRepository,
        Logger $logger,
    ): void {
        while (true) {
            try {
                $webSocketClient = $this->createWebSocketClient($configRepository);
                $webSocketClient->receive();

                while ($webSocketClient->isConnected()) {
                    $message = $webSocketClient->receive();

                    $messageContent = $this->parseMessageContent($message);
                    if ($messageContent === null) {
                        continue;
                    }

                    $eventName = $messageContent['event'];
                    if ($eventName !== self::EVENT_NAME_STATUS_CHANGE) {
                        continue;
                    }

                    $this->handleOrderStatusChangeEvent(
                        $messageContent['data'],
                        $transactionRepository,
                        $transactionStatusMapper,
                        $logger
                    );
                }

            } catch (ConnectionException $exception) {
                $this->handleWebSocketConnectionException($exception);

            } finally {
                if (isset($webSocketClient)) {
                    $webSocketClient->close();
                }
            }
        }
    }

    private function handleOrderStatusChangeEvent(
        array $eventPayload,
        TransactionRepository $transactionRepository,
        TransactionStatusMapper $transactionStatusMapper,
        Logger $logger,
    ): void {
        if (!is_string($eventPayload['custom_id'] ?? null) || !is_string($eventPayload['offer_status'] ?? null)) {
            $this->logUnexpectedEventPayload($eventPayload, $logger);
            return;
        }

        $transaction = $transactionRepository->findByUuid($eventPayload['custom_id']);
        if ($transaction === null) {
            $this->logTransactionNotFound($eventPayload, $logger);
            return;
        }

        $transactionStatus = $transactionStatusMapper->mapFromMarketOfferStatus($eventPayload['offer_status']);
        $transaction->setStatus($transactionStatus);
        $transaction->save();

        if (in_array($transactionStatus, self::TRANSACTION_STATUSES_TO_NOTIFY_USERS_ABOUT_FAILED_TRANSACTIONS, true)) {
            SendFailedTransactionMailJob::dispatch($transaction->getId());
            SendTransactionAdminMailJob::dispatch($transaction->getId());
        }

        if (in_array($transactionStatus, self::TRANSACTION_STATUSES_TO_NOTIFY_USERS_ABOUT_SUCCESS_TRANSACTIONS, true)) {
            SendSuccessTransactionMailJob::dispatch($transaction->getId());
        }

        $this->logUpdatedTransaction($transaction, $logger);
    }

    private function parseMessageContent(mixed $message): array|null
    {
        if ($message instanceof Message) {
            $message = $message->getContent();
        }

        if (!is_string($message)) {
            return null;
        }

        return json_decode($message, true);
    }

    private function createWebSocketClient(ConfigRepository $configRepository): WebSocketClient
    {
        $shopId = $configRepository->get('services.marketplace.credentials.client_id');
        $secretKey = $configRepository->get('services.marketplace.credentials.secret_key');
        $signature = md5($shopId.$secretKey);

        $queryParameters = http_build_query(['shopid' => $shopId, 'signature' => $signature]);

        $connectionUrl = sprintf('%s?%s', self::BASE_URL_MARKET_WEB_SOCKET_HOST, $queryParameters);

        return new WebSocketClient($connectionUrl, ['timeout' => 86400]);
    }

    private function logUpdatedTransaction(Transaction $transaction, Logger $logger): void
    {
        $logger->info(
            sprintf(
                'Transaction (id=%s) status has been updated to "%s"',
                $transaction->getId(),
                $transaction->getStatus()?->toString(),
            )
        );

        $this->info(
            sprintf(
                'Transaction status has been updated. Details: %s',
                json_encode([
                    'transaction_id' => $transaction->getId(),
                    'transaction_status' => $transaction->getStatus()?->toString(),
                ])
            )
        );
    }

    private function logUnexpectedEventPayload(array $eventPayload, Logger $logger): void
    {
        $logger->emergency('Unexpected `offer_status` websocket event payload!', ['event_payload' => $eventPayload]);

        $this->error(
            sprintf(
                'Unexpected `offer_status` websocket event payload!. Event Payload: %s',
                json_encode([
                    'event_payload' => $eventPayload,
                ])
            )
        );
    }

    private function logTransactionNotFound(array $eventPayload, Logger $logger): void
    {
        $logger->emergency(
            'Transaction specified in the websocket event is not found in the database.',
            ['event_payload' => $eventPayload]
        );

        $this->error(
            sprintf(
                'Transaction specified in the websocket event is not found in the database. Event Payload: %s',
                json_encode([
                    'event_payload' => $eventPayload,
                ])
            ),
        );
    }

    private function handleWebSocketConnectionException(ConnectionException $exception): void
    {
        // do nothing

//        $this->warn(
//            sprintf(
//                'WebSocket Client failed with a ConnectionException. Trying to process next message. Details: %s',
//                json_encode([
//                    'message' => $exception->getMessage(),
//                    'code' => $exception->getCode(),
//                    'data' => $exception->getData(),
//                ])
//            )
//        );
    }
}
