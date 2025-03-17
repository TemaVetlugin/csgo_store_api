<?php

declare(strict_types=1);

namespace App\Factories;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Uri;
use Ilzrv\LaravelSteamAuth\SteamAuthenticator;

class SteamAuthenticatorFactory
{
    public function __construct(
        private readonly GuzzleClient $httpClient,
        private readonly HttpFactory $httpFactory,
    ) {
    }

    public function getForRedirectToSteam(): SteamAuthenticator
    {
        return new SteamAuthenticator(new Uri(), $this->httpClient, $this->httpFactory);
    }

    public function getForCallbackUri(string $steamCallbackUri): SteamAuthenticator
    {
        return new SteamAuthenticator(
            new Uri($steamCallbackUri),
            $this->httpClient,
            $this->httpFactory
        );
    }
}
