<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\MaintainerNotification\InsufficientFundsOnMarketplaceAccountMail;
use App\Services\Market\Provider\BalanceProvider;
use App\Services\Market\Provider\CurrencyProvider;
use App\Services\Order\OrderProvider;
use Illuminate\Bus\Queueable;
use Illuminate\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Mail\MailManager;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendInsufficientFundsOnMarketplaceAccountMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly string $userEmail,
        private readonly array $productNames,
        private readonly float $orderAmount,
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(
        ConfigRepository $configRepository,
        BalanceProvider $balanceProvider,
        OrderProvider $orderProvider,
        CurrencyProvider $currencyProvider,
        MailManager $mailManager
    ): void {
        $maintainerEmailAddresses = $configRepository->get('maintenance.email_addresses');

        $balanceAmount = $balanceProvider->getBalance($currencyProvider->getMarketplaceCurrencyCode());
        $pendingOrdersTotalAmount = $orderProvider->getPendingOrdersTotalAmount();

        $mail = new InsufficientFundsOnMarketplaceAccountMail(
            $this->userEmail,
            $this->productNames,
            $this->orderAmount,
            $balanceAmount->getAmount() - $pendingOrdersTotalAmount->getAmount(),
        );

        $mailManager
            ->to($maintainerEmailAddresses)
            ->send($mail)
        ;
    }
}
