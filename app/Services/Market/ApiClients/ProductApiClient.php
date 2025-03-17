<?php

declare(strict_types=1);

namespace App\Services\Market\ApiClients;

use App\Entity\DTO\Market\BuyProductApiPayload;
use App\Exceptions\Market\MarketApiException;

class ProductApiClient
{
    private const PARAMETER_KEY_FULL = 'full';
    private const PARAMETER_KEY_EXTENDED = 'extended';

    private const METHOD_GET_ITEMS = 'market_pricelist';
    private const METHOD_BUY_ITEM = 'market_buy';

    public function __construct(private readonly HttpClient $httpClient)
    {
    }

    /**
     * @see https://skinsback.com/docs/market_pricelist
     */
    public function getProducts(): array
    {
        $requestParameters = [
            self::PARAMETER_KEY_FULL => 1,
            self::PARAMETER_KEY_EXTENDED => 1,
        ];

        $response = $this->httpClient->request(self::METHOD_GET_ITEMS, $requestParameters);
//	logger(['response' => $response]);
        if (!isset($response['status'], $response['items']) || $response['status'] !== 'success') {
            throw new MarketApiException(
                'Invalid Market API response received.',
                (isset($response['error_code'])) ? (int) $response['error_code'] : null,
            );
        }

        return $response['items'];
    }

    /**
     * @see https://skinsback.com/docs/market_buy
     */
    public function buyProduct(BuyProductApiPayload $buyProductPayload): array
    {
        $requestParameters = [
            'id' => $buyProductPayload->getProductId(),
            'custom_id' => $buyProductPayload->getCustomTransactionId(),
            'max_price' => $buyProductPayload->getMaxPrice(),
            'partner' => $buyProductPayload->getPartner(),
            'token' => $buyProductPayload->getToken(),
        ];

        $response = $this->httpClient->request(self::METHOD_BUY_ITEM, $requestParameters);

        if (!isset($response['status'], $response['buy_id']) || $response['status'] !== 'success') {
            throw new MarketApiException(
                message: 'Failed to buy product due to Market API error.',
                apiErrorCode: (isset($response['error_code'])) ? (int) $response['error_code'] : null,
                apiResponse: $response,
            );
        }

        return $response;
    }
}
