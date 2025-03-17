<?php

declare(strict_types=1);

namespace App\Entity\VO;

class Money
{
    public function __construct(
        private readonly float $amount,
        private readonly string $currencyCode,
    ) {
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }
}
