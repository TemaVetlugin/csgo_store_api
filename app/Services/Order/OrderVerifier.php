<?php

declare(strict_types=1);

namespace App\Services\Order;

use App\Entity\VO\Money;
use App\Exceptions\Market\BuyProduct\BuyProductException;
use App\Exceptions\Market\BuyProduct\Case\BuyProductExceptionCase;
use App\Exceptions\Market\BuyProduct\ProductAreOutOfStockException;
use App\Exceptions\Market\User\Case\UserIsNotTradableExceptionCase;
use App\Exceptions\Market\User\UserIsNotTradableException;
use App\Models\User;
use App\Services\Market\Manager\BalanceManager;
use App\Services\Market\Provider\CurrencyProvider;
use App\Services\Market\Provider\ProductProvider;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class OrderVerifier
{
    public function __construct(
        private readonly ProductProvider $productProvider,
        private readonly CurrencyProvider $currencyProvider,
        private readonly BalanceManager $balanceManager,
    ) {
    }

    public function ensureUserCanByProducts(User $user, array $productIds): void
    {
        $foundProducts = $this->productProvider->getByIds($productIds);

        $this->ensureProductsAreAvailable($productIds, $foundProducts);
        $this->ensureUserIsTradable($user);
        $this->ensureMarketAccountBalanceIsEnough($foundProducts);
    }

    /**
     * @throws ProductAreOutOfStockException
     */
    private function ensureProductsAreAvailable(array $requestedProductIds, array $foundProducts): void
    {
        $foundProductIds = array_column($foundProducts, 'id');
        $unavailableProductIds = array_diff($requestedProductIds, $foundProductIds);

        if (count($unavailableProductIds) > 0) {
            throw new ProductAreOutOfStockException(
                'Some of the selected products are out of stock and no longer available to buy.',
                $unavailableProductIds
            );
        }
    }

    /**
     * @throws BuyProductException
     */
    private function ensureMarketAccountBalanceIsEnough(array $products): void
    {
        $productsTotalPrice = (float) array_sum(
            array_map(static fn (array $product) => $product['price']->getAmount(), $products)
        );
        $marketCurrencyCode = $this->currencyProvider->getMarketplaceCurrencyCode();

        $requiredAmount = new Money($productsTotalPrice, $marketCurrencyCode);

        if (!$this->balanceManager->isEnoughBalance($requiredAmount)) {
            throw new BuyProductException(
                'The system cannot process such a large order at the moment. Please try to reduce the amount.',
                BuyProductExceptionCase::InsufficientFundsOnMarketAccount,
                $products
            );
        }
    }

    /**
     * @throws UserIsNotTradableException
     */
    private function ensureUserIsTradable(User $user): void
    {
        if ($user->getSteamTradeLink() === null) {
            throw new UserIsNotTradableException(
                'User cannot buy products because he does not have a valid steam trade link set.',
                UserIsNotTradableExceptionCase::SteamTradeLinkIsNotSet,
            );
        }
        if ($user->getEmail() !== null) {
            $url = URL::temporarySignedRoute(
                'verification.verify',
                Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
                [
                    'id' => $user->getKey(),
                    'hash' => sha1($user->getEmailForVerification()),
                ]
            );
            $user->sendEmailVerificationNotification();
        }
        if ($user->getEmailVerifiedAt() === null) {
            throw new UserIsNotTradableException(
                'User cannot buy products because he does not have a verified email address set.',
                UserIsNotTradableExceptionCase::EmailIsNotVerified,
            );
        }
    }
}
