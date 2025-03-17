<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Entity\Events\PaymentProcessed;
use App\Services\Market\ProductBuyer;
use Illuminate\Log\Logger;

class PaymentProcessedListener
{
    public function __construct(
        private readonly ProductBuyer $productBuyer,
        private readonly Logger $logger,
    ) {
    }

    public function handle(PaymentProcessed $event): void
    {
        $this->productBuyer->processOrder($event->getOrder());

        $this->logger->info('Order successfully processed.', ['orderId' => $event->getOrder()->getId()]);
    }
}
