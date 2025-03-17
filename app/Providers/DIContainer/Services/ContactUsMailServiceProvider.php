<?php

declare(strict_types=1);

namespace App\Providers\DIContainer\Services;

use App\Services\Mail\ContactUsMailService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Mail\MailManager;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class ContactUsMailServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function provides(): array
    {
        return [
            ContactUsMailService::class,
        ];
    }

    public function register(): void
    {
        $this->registerContactUsMailService();
    }

    private function registerContactUsMailService(): void
    {
        $callback = static function (Application $app) {
            return new ContactUsMailService(
                $app->make(MailManager::class),
                Config::get('maintenance.email_addresses'),
            );
        };

        $this->app->singleton(ContactUsMailService::class, $callback);
    }
}
