<?php

declare(strict_types=1);

namespace App\Services\Market\ApiClients;

use Illuminate\Support\Facades\Http;

class HttpClient
{
    private const BASE_URL = 'https://skinsback.com/api.php';

    private const PARAMETER_KEY_METHOD = 'method';
    private const PARAMETER_KEY_CLIENT_ID = 'shopid';
    private const PARAMETER_KEY_SIGNATURE = 'sign';

    public function __construct(
        private readonly string $clientId,
        private readonly string $secretKey,
    )
    {
    }

    public function request(string $method, array $payload = []): array
    {
        $defaultRequestParameters = $this->makeDefaultParameters($method);
        logger(['check' => 'awdawd']);
        $payload = array_merge($defaultRequestParameters, $payload);
        $payload = $this->signRequestPayload($payload);
//logger(['aaa' => Http::post(self::BASE_URL, $payload)]);
        $response = Http::withOptions(['stream' => true])->timeout(12312312312)->post(self::BASE_URL, $payload)->json();
//logger(['la' =>$response]);
        return $response;
    }

    private function makeDefaultParameters(string $method): array
    {
        return [
            self::PARAMETER_KEY_METHOD => $method,
            self::PARAMETER_KEY_CLIENT_ID => $this->clientId,
        ];
    }

    private function signRequestPayload(array $payload): array
    {
        ksort($payload);

        $serializedParameters = '';
        foreach ($payload as $itemKey => $itemValue) {
            $serializedParameters .= sprintf('%s:%s;', $itemKey, $itemValue);
        }

        $payload[self::PARAMETER_KEY_SIGNATURE] = hash_hmac('sha1', $serializedParameters, $this->secretKey);
        logger($payload);
        return $payload;
    }
}
