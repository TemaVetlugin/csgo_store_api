<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Market\Order;

use App\Exceptions\Market\BuyProduct\BuyProductException;
use App\Exceptions\Market\BuyProduct\Case\BuyProductExceptionCase;
use App\Exceptions\Market\BuyProduct\ProductAreOutOfStockException;
use App\Http\Requests\Api\Order\CreateOrderRequest;
use App\Http\Resources\Api\OrderResource;
use App\Jobs\SendInsufficientFundsOnMarketplaceAccountMailJob;
use App\Services\Market\Manager\MarketCacheManager;
use App\Services\Order\OrderManager;
use App\Services\Order\OrderVerifier;
use App\Services\Payment\PaymentManager;
use App\Services\User\UserProvider;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class CreateOrderController extends Controller
{
    public function __construct(
        private readonly MarketCacheManager $marketCacheManager,
        private readonly UserProvider $userProvider,
        private readonly OrderManager $orderManager,
    ) {
    }

    public function __invoke(CreateOrderRequest $request): JsonResponse
    {
        $this->marketCacheManager->refreshCache();

        $authenticatedUser = $this->userProvider->getAuthenticated();
        $productIds = $request->getProductIds();

        $order = $this->orderManager->createOrder($productIds, $authenticatedUser);
        if ($order->getTotalPrice()==0) {
            throw new ProductAreOutOfStockException(
                'Some of the selected products are out of stock and no longer available to buy.',$productIds
            );
        }
        if ($order->getTotalPrice()<1) {
            throw new BuyProductException('minimum order amount 1 euro', BuyProductExceptionCase::OrderTooSmallFailed);
        }
        return response()->json(new OrderResource($order));
    }
}
