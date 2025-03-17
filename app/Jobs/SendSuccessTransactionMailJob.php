<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Repositories\User\TransactionRepository;
use App\Services\Mail\TransactionMailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Log\Logger;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendSuccessTransactionMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private readonly int $transactionId)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(
        TransactionRepository $transactionRepository,
        TransactionMailService $transactionMailService,
        Logger $logger,
    ): void {
            $transaction = $transactionRepository->find($this->transactionId);
            if ($transaction === null) {
                $this->logTransactionNotFound($logger);
                return;
            }
            $this->logTransactionNotFound($logger);
            $transactionMailService->sendSuccessTransactionMailToUser($transaction->getOrder()->getUser(), $transaction);

    }

    private function logTransactionNotFound(Logger $logger): void
    {
        $logger->error('Transaction is not found!', ['transaction_id' => $this->transactionId]);
    }
}
