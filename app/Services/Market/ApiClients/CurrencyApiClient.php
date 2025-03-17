<?php

declare(strict_types=1);

namespace App\Services\Market\ApiClients;

use App\Exceptions\Market\MarketApiException;

class CurrencyApiClient
{
    private const METHOD_GET_CURRENCIES = 'currencies';

    public function __construct(private readonly HttpClient $httpClient)
    {
    }

    /**
     * @see https://skinsback.com/docs/currencies
     */
    public function getCurrencies(): array
    {
        $response = $this->httpClient->request(self::METHOD_GET_CURRENCIES);

        if (!isset($response['status'], $response['items']) || $response['status'] !== 'success') {
            throw new MarketApiException(
                message: 'Invalid Market API response received.',
                apiErrorCode: (isset($response['error_code'])) ? (int) $response['error_code'] : null,
                apiResponse: $response
            );
        }

        return $response['items'];
    }
}
