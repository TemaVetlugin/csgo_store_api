<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Order;
use App\Repositories\User\TransactionRepository;
use App\Services\Mail\TransactionMailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Log\Logger;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendOrderAdminMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private readonly Order $order)
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
            $transactionMailService->sendOrderMailToAdmin( $this->order);

    }

    private function logTransactionNotFound(Logger $logger): void
    {
        $logger->error('Transaction is not found!', ['transaction_id' => '123']);
    }
}
