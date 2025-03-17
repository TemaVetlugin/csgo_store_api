<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Payment;

use App\Entity\DTO\Payment\PaymentCallbackPayload;
use App\Http\Requests\Api\Payment\CallbackRequest;
use App\Http\Requests\Api\Payment\GetStatusRequest;
use App\Models\Order;
use App\Providers\DIContainer\Services\PaymentServiceProvider;
use App\Services\Order\OrderManager;
use App\Services\Order\OrderProvider;
use App\Services\Payment\CallbackAuthenticityVerifier;
use App\Services\Payment\PaymentManager;
use Illuminate\Log\Logger;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class PaymentGetStatusController extends Controller
{
    public function __construct(
        private readonly PaymentManager $paymentManager,
        private readonly OrderProvider $orderProvider,
    ) {
    }

    /**
     * @throws ValidationException
     */
    public function __invoke(GetStatusRequest $request): Response
    {
        $getStatusPayload = $request->getUuid();

        $order = $this->orderProvider->findByUuid($getStatusPayload);

        if ($order === null) {
            return new Response(status: Response::HTTP_NOT_FOUND);
        }

        $status = $this->paymentManager->checkPaymentStatus($order);

        return response()->json($status);
    }

}
