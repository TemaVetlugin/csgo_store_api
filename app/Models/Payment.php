<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Enum\PaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Payment extends Model
{
    protected $casts = [
        'status' => PaymentStatus::class,
        'status_changed_at' => 'datetime',
        'amount' => 'double',
        'fee' => 'double',
    ];

    public function getId(): int|null
    {
        return $this->id;
    }

    public function getExternalId(): string
    {
        return $this->external_id;
    }

    public function setExternalId(string $externalId): static
    {
        $this->external_id = $externalId;

        return $this;
    }

    public function getPaymentService(): string|null
    {
        return $this->payment_service;
    }

    public function setPaymentService(string $paymentService): static
    {
        $this->payment_service = $paymentService;

        return $this;
    }

    public function getStatus(): PaymentStatus
    {
        return $this->status;
    }

    public function setStatus(PaymentStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getStatusChangedAt(): Carbon
    {
        return $this->status_changed_at;
    }

    public function setStatusChangedAt(Carbon $statusChangedAt): static
    {
        $this->status_changed_at = $statusChangedAt;

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

    public function getCurrency(): string|null
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): static
    {
        $this->currency = $currency;

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

    public function getResolution(): string
    {
        return $this->resolution;
    }

    public function setResolution(string $resolution): static
    {
        $this->resolution = $resolution;

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
