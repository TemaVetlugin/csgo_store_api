<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('clear', function () {
    Artisan::call('cache:clear', outputBuffer: $this->getOutput());
    Artisan::call('config:clear', outputBuffer: $this->getOutput());
    Artisan::call('config:cache', outputBuffer: $this->getOutput());
    Artisan::call('view:clear', outputBuffer: $this->getOutput());
    Artisan::call('optimize:clear', outputBuffer: $this->getOutput());

    opcache_reset();
});
