<?php

declare(strict_types=1);

namespace App\Providers\DIContainer\Controllers;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\VerifyUserEmailController;
use App\Services\User\UserManager;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class AuthenticationControllerServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function provides(): array
    {
        return [
            HomeController::class,
            LogoutController::class,
            VerifyUserEmailController::class,
        ];
    }

    public function register(): void
    {
        $this->registerHomeController();
        $this->registerLogoutController();
        $this->registerVerifyUserEmailController();
    }

    private function registerHomeController(): void
    {
        $callback = static function (Application $app) {
            return new HomeController(
                $app->make(Redirector::class),
                Config::get('auth.home_page_redirect_url'),
            );
        };

        $this->app->singleton(HomeController::class, $callback);
    }

    private function registerLogoutController(): void
    {
        $callback = static function (Application $app) {
            return new LogoutController(
                $app->make(UserManager::class),
                $app->make(Redirector::class),
                Config::get('auth.home_page_redirect_url'),
            );
        };

        $this->app->singleton(LogoutController::class, $callback);
    }

    private function registerVerifyUserEmailController(): void
    {
        $callback = static function (Application $app) {
            return new VerifyUserEmailController(
                $app->make(Redirector::class),
                Config::get('auth.home_page_redirect_url'),
            );
        };

        $this->app->singleton(VerifyUserEmailController::class, $callback);
    }
}
