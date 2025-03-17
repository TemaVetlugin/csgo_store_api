<?php

declare(strict_types=1);

namespace App\Services\Market\ApiClients;

use App\Exceptions\Market\MarketApiException;

class AccountApiClient
{
    private const METHOD_GET_BALANCE = 'balance';

    public function __construct(private readonly HttpClient $httpClient)
    {
    }

    /**
     * @see https://skinsback.com/docs/balance
     */
    public function getBalance(): array
    {
        $response = $this->httpClient->request(self::METHOD_GET_BALANCE);

        if (!isset($response['status'], $response['balance_in_currencies']) || $response['status'] !== 'success') {
            throw new MarketApiException(
                message: 'Invalid Market API response received.',
                apiErrorCode: (isset($response['error_code'])) ? (int) $response['error_code'] : null,
                apiResponse: $response
            );
        }

        return $response['balance_in_currencies'];
    }
}
