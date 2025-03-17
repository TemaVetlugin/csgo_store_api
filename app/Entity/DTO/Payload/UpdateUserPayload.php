<?php

declare(strict_types=1);

namespace App\Entity\DTO\Payload;

class UpdateUserPayload
{
    private string|null $email;
    private string|null $steamTradeLink;

    public function __construct()
    {
        $this->email = null;
        $this->steamTradeLink = null;
    }

    public function getEmail(): string|null
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getSteamTradeLink(): string|null
    {
        return $this->steamTradeLink;
    }

    public function setSteamTradeLink(string $steamTradeLink): static
    {
        $this->steamTradeLink = $steamTradeLink;

        return $this;
    }
}
