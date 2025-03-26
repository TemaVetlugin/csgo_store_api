<?php

declare(strict_types=1);

namespace App\Services\Market\Manager;

use App\Entity\VO\Money;
use App\Services\Market\Provider\BalanceProvider;
use App\Services\Order\OrderProvider;

class BalanceManager
{
    public function __construct(
        private readonly BalanceProvider $balanceProvider,
        private readonly OrderProvider $orderProvider,
    ) {
    }

    public function isEnoughBalance(Money $money): bool
    {
        $balanceAmount = $this->balanceProvider->getBalance($money->getCurrencyCode());
        $pendingOrdersTotalAmount = $this->orderProvider->getPendingOrdersTotalAmount();

        return ($balanceAmount->getAmount()) > $money->getAmount();
//        return ($balanceAmount->getAmount() - $pendingOrdersTotalAmount->getAmount()) > $money->getAmount();
    }
}
