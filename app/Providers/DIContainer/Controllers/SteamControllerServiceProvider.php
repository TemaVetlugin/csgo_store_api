<?php

declare(strict_types=1);

namespace App\Providers\DIContainer\Controllers;

use App\Factories\SteamAuthenticatorFactory;
use App\Http\Controllers\Auth\Steam\SteamCallbackController;
use App\Http\Controllers\Auth\Steam\SteamLogInController;
use App\Repositories\SteamUserRepository;
use Illuminate\Auth\AuthManager;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class SteamControllerServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function provides(): array
    {
        return [
            SteamLogInController::class,
            SteamCallbackController::class,
        ];
    }

    public function register(): void
    {
        $steamAuthenticatorFactory = $this->app->make(SteamAuthenticatorFactory::class);

        $this->registerSteamLogInController($steamAuthenticatorFactory);
        $this->registerSteamCallbackController($steamAuthenticatorFactory);
    }

    private function registerSteamLogInController(SteamAuthenticatorFactory $steamAuthenticatorFactory): void
    {
        $callback = static function (Application $app) use ($steamAuthenticatorFactory) {
            return new SteamLogInController(
                $steamAuthenticatorFactory->getForRedirectToSteam(),
                $app->make(Redirector::class),
            );
        };

        $this->app->singleton(SteamLogInController::class, $callback);
    }

    private function registerSteamCallbackController(SteamAuthenticatorFactory $steamAuthenticatorFactory): void
    {
        $callback = static function (Application $app) use ($steamAuthenticatorFactory) {
            return new SteamCallbackController(
                $steamAuthenticatorFactory->getForCallbackUri(request()->getUri()),
                $app->make(SteamUserRepository::class),
                $app->make(AuthManager::class),
                $app->make(Redirector::class),
                Config::get('auth.home_page_redirect_url'),
            );
        };

        $this->app->singleton(SteamCallbackController::class, $callback);
    }
}
