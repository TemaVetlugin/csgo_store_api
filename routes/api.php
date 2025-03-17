<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/market/products', \App\Http\Controllers\Api\Market\Product\GetProductsController::class)
    ->name('api.market.products')
;

Route::get('/market/products/popular', \App\Http\Controllers\Api\Market\Product\GetPopularProductsController::class)
    ->name('api.market.products.popular')
;

Route::post('/contact-us', \App\Http\Controllers\Api\ContactUsController::class)
    ->middleware('throttle:contact-us')
    ->name('api.contact-us')
;

Route::post('/payment/callback', \App\Http\Controllers\Api\Payment\PaymentCallbackController::class)
    ->name('api.payment.callback')
;

Route::middleware(['auth:sanctum'])->group(static function () {
    Route::get('/user', \App\Http\Controllers\Api\User\GetCurrentUserProfileController::class)
        ->name('api.user.get')
    ;

    Route::get('/user/email', \App\Http\Controllers\Api\User\VerifyEmailController::class)
        ->name('api.user.email')
    ;

    Route::get('/user/transactions', \App\Http\Controllers\Api\User\GetUserTransactionsController::class)
        ->name('api.user.transactions.get')
    ;

    Route::patch('/user', \App\Http\Controllers\Api\User\UpdateCurrentUserProfileController::class)
        ->name('api.user.update')
    ;

    Route::post('/user/complete-registration', \App\Http\Controllers\Api\User\CompleteUserRegistrationController::class)
        ->name('api.user.complete-registration')
    ;

    Route::post('/orders', \App\Http\Controllers\Api\Market\Order\CreateOrderController::class)
        ->name('api.orders.create')
    ;

    Route::get('/orders/price', \App\Http\Controllers\Api\Market\Order\GetOrderPriceController::class)
        ->name('api.orders.price')
    ;

    Route::get('/orders/details', \App\Http\Controllers\Api\Market\Order\GetOrderDetailsController::class)
        ->name('api.orders.price')
    ;

    Route::post('/payment', \App\Http\Controllers\Api\Payment\CreatePaymentController::class)
        ->name('api.payment.create')
    ;

    Route::get('/payment/status', \App\Http\Controllers\Api\Payment\PaymentGetStatusController::class)
        ->name('api.payment.status')
    ;
});
