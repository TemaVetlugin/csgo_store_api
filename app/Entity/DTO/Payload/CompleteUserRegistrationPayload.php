<?php

declare(strict_types=1);

namespace App\Entity\DTO\Payload;

class CompleteUserRegistrationPayload
{
    public function __construct(
        private readonly string $email,
        private readonly string $steamTradeLink,
    ) {
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getSteamTradeLink(): string
    {
        return $this->steamTradeLink;
    }
}
