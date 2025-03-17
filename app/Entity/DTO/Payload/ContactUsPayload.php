<?php

declare(strict_types=1);

namespace App\Entity\DTO\Payload;

class ContactUsPayload
{
    public function __construct(
        private readonly string $userEmail,
        private readonly string $message,
    ) {
    }

    public function getUserEmail(): string
    {
        return $this->userEmail;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
