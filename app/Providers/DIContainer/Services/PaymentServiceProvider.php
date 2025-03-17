<?php

declare(strict_types=1);

namespace App\Providers\DIContainer\Services;

use App\Services\Payment\CallbackAuthenticityVerifier;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function provides(): array
    {
        return [
            CallbackAuthenticityVerifier::class,
        ];
    }

    public function register(): void
    {
        $this->registerCallbackAuthenticityVerifier();
    }

    private function registerCallbackAuthenticityVerifier(): void
    {
        $callback = static function (Application $app) {
            return new CallbackAuthenticityVerifier(
                Config::get('services.payment.secret_key'),
                Config::get('services.payment.allowed_callback_ips'),
            );
        };

        $this->app->singleton(CallbackAuthenticityVerifier::class, $callback);
    }
}
