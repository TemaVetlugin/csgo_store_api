<?php

declare(strict_types=1);

namespace App\Entity\DTO\Payment;

use App\Models\Enum\PaymentStatus;

class PaymentCallbackPayload
{
    private string $resolution;
    private string $paymentMethod;
    private float $amount;
    private float $fee;
    private string $currency;
    private string|null $token = null;

    public function __construct(
        private readonly string $paymentId,
        private readonly string $referenceId,
        private readonly PaymentStatus $status,
        private readonly int $timestamp,
    ) {
    }

    public function getPaymentId(): string
    {
        return $this->paymentId;
    }

    public function getReferenceId(): string
    {
        return $this->referenceId;
    }

    public function getStatus(): PaymentStatus
    {
        return $this->status;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    public function getResolution(): string|null
    {
        return $this->resolution;
    }

    public function setResolution(string $resolution): static
    {
        $this->resolution = $resolution;

        return $this;
    }

    public function getPaymentMethod(): string|null
    {
        return $this->paymentMethod;
    }

    public function setPaymentMethod(string $paymentMethod): static
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    public function getAmount(): float|null
    {
        return $this->amount;
    }

    public function setAmount(float $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getFee(): float|null
    {
        return $this->fee;
    }

    public function setFee(float $fee): static
    {
        $this->fee = $fee;

        return $this;
    }

    public function getCurrency(): string|null
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): static
    {
        $this->currency = $currency;

        return $this;
    }

    public function getToken(): string|null
    {
        return $this->token;
    }

    public function setToken(string $token): static
    {
        $this->token = $token;

        return $this;
    }
}
