<?php

declare(strict_types=1);

namespace App\Entity\DTO\Payment;

use App\Models\Enum\PaymentStatus;

class GetStatusPayload
{
    private string $uuid;

    public function __construct(
    ) {
    }

    public function getUuid(): string|null
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): static
    {
        $this->uuid = $uuid;

        return $this;
    }
}
