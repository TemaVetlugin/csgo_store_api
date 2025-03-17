<?php

declare(strict_types=1);

namespace App\Entity\DTO\Steam;

class SteamTradeLinkPayload
{
    public function __construct(
        private readonly string $partner,
        private readonly string $token,
    ) {
    }

    public function getPartner(): string
    {
        return $this->partner;
    }

    public function getToken(): string
    {
        return $this->token;
    }
}
