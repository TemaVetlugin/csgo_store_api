<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Market\Order;

use App\Exceptions\Market\BuyProduct\BuyProductException;
use App\Http\Requests\Api\Order\CreateOrderRequest;
use App\Http\Requests\Api\Order\GetOrderPriceRequest;
use App\Http\Resources\Api\OrderResource;
use App\Jobs\SendInsufficientFundsOnMarketplaceAccountMailJob;
use App\Services\Market\Manager\MarketCacheManager;
use App\Services\Order\OrderManager;
use App\Services\Order\OrderProvider;
use App\Services\Order\OrderVerifier;
use App\Services\Payment\PaymentManager;
use App\Services\User\UserProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class GetOrderPriceController extends Controller
{
    public function __construct(
        private readonly OrderProvider $orderProvider,
    ) {
    }

    public function __invoke(GetOrderPriceRequest $request): JsonResponse
    {
        $uuid = $request->getUuid();
        $order = $this->orderProvider->findByUuid($uuid);

        return response()->json(['price' => $order->getTotalPrice()]);
    }
}
