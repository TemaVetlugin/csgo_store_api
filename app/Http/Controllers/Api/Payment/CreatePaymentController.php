<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Payment;

use App\Exceptions\Market\BuyProduct\BuyProductException;
use App\Http\Requests\Api\Payment\CreatePaymentRequest;
use App\Jobs\SendInsufficientFundsOnMarketplaceAccountMailJob;
use App\Models\Enum\OrderStatus;
use App\Services\Order\OrderProvider;
use App\Services\Order\OrderVerifier;
use App\Services\Payment\PaymentManager;
use App\Services\User\UserProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class CreatePaymentController extends Controller
{
    public function __construct(
        private readonly OrderProvider $orderProvider,
        private readonly UserProvider $userProvider,
        private readonly PaymentManager $paymentManager,
        private readonly OrderVerifier $orderVerifier,
    ) {
    }

    public function __invoke(CreatePaymentRequest $request): JsonResponse
    {
        $authenticatedUser = $this->userProvider->getAuthenticated();
        $uuid = $request->getUuid();
        $data = $request->getOrderData();
        $order = $this->orderProvider->updateOrderData($data, $this->orderProvider->findByUuid($uuid));
        $transactions = $order->getTransactions();
        $productIds = [];
        foreach ($transactions as $transaction){
            $productIds[] = $transaction['product_id'];
        }

        $order->setStatus(OrderStatus::Pending);
        $order->save();

        try {
            $this->orderVerifier->ensureUserCanByProducts($authenticatedUser, $productIds);
        } catch (BuyProductException $exception) {
            if ($authenticatedUser->getEmail() !== null) {
                $this->sendInsufficientFundsNotificationToMaintainer($authenticatedUser->getEmail(), $exception->getProducts());
            }
            $order->setStatus(OrderStatus::Failed);
            $order->save();
            throw $exception;
        }

        $url = $this->paymentManager->createPayment($order, $authenticatedUser);

        return response()->json($url);
    }



    private function sendInsufficientFundsNotificationToMaintainer(string $customerEmail, array $products): void
    {
        $productNames = [];
        $orderAmount = 0;

        foreach ($products as $product) {
            $productNames[] = $product['name'];
            $orderAmount += $product['price']->getAmount();
        }

        SendInsufficientFundsOnMarketplaceAccountMailJob::dispatch($customerEmail, $productNames, (float) $orderAmount);
    }
}
