<?php

declare(strict_types=1);

namespace App\Entity\DTO\Market;

class BuyProductApiPayload
{
    public function __construct(
        private readonly string $productId,
        private readonly string $tradeLinkPartner,
        private readonly string $tradeLinkToken,
        private readonly float $maxPriceToBuy,
        private readonly string $customTransactionId,
    ) {
    }

    public function getProductId(): string
    {
        return $this->productId;
    }

    public function getPartner(): string
    {
        return $this->tradeLinkPartner;
    }

    public function getToken(): string
    {
        return $this->tradeLinkToken;
    }

    public function getMaxPrice(): float
    {
        return $this->maxPriceToBuy;
    }

    public function getCustomTransactionId(): string
    {
        return $this->customTransactionId;
    }
}
