<?php

declare(strict_types=1);

namespace App\Exceptions\Market;

use RuntimeException;

class MarketApiException extends RuntimeException
{
    public function __construct(
        string $message,
        private readonly int|null $apiErrorCode = null,
        private readonly array|null $apiResponse = [],
    ) {
        parent::__construct($message);
    }

    public function getApiErrorCode(): int|null
    {
        return $this->apiErrorCode;
    }

    public function getApiResponse(): array|null
    {
        return $this->apiResponse;
    }
}
