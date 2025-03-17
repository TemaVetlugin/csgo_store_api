<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/auth/steam/login', App\Http\Controllers\Auth\Steam\SteamLogInController::class)
    ->name('auth.steam.login')
;

Route::get('/auth/steam/login/callback', App\Http\Controllers\Auth\Steam\SteamCallbackController::class)
    ->name('auth.steam.login.callback')
;

Route::get('/login', App\Http\Controllers\LoginController::class)
    ->name('login')
;
Route::middleware(['auth:sanctum'])->group(static function () {

    Route::get('/logout', App\Http\Controllers\LogoutController::class)
        ->name('logout')
    ;

    Route::get('/email/verify/{id}/{hash}', App\Http\Controllers\VerifyUserEmailController::class)
        ->middleware(['signed'])
        ->name('verification.verify')
    ;
});

Route::get('/', App\Http\Controllers\HomeController::class)
    ->name('home')
;
