<?php

declare(strict_types=1);

namespace App\Providers\DIContainer\Services;

use App\Repositories\Market\CurrencyRepository;
use App\Services\Market\ApiClients\HttpClient;
use App\Services\Market\Provider\CurrencyProvider;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class MarketplaceServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function provides(): array
    {
        return [
            HttpClient::class,
            CurrencyProvider::class,
        ];
    }

    public function register(): void
    {
        $this->registerMarketplaceClient();
        $this->registerCurrencyProvider();
    }

    private function registerMarketplaceClient(): void
    {
        $callback = static function (Application $app) {
            return new HttpClient(
                Config::get('services.marketplace.credentials.client_id'),
                Config::get('services.marketplace.credentials.secret_key'),
            );
        };

        $this->app->singleton(HttpClient::class, $callback);
    }

    private function registerCurrencyProvider(): void
    {
        $callback = static function (Application $app) {
            return new CurrencyProvider(
                $app->make(CurrencyRepository::class),
                Config::get('client.default_currency'),
                Config::get('services.marketplace.currency_code'),
            );
        };

        $this->app->singleton(CurrencyProvider::class, $callback);
    }
}
