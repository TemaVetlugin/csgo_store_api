<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Payment;

use App\Entity\DTO\Payment\PaymentCallbackPayload;
use App\Http\Requests\Api\Payment\CallbackRequest;
use App\Models\Order;
use App\Services\Order\OrderManager;
use App\Services\Order\OrderProvider;
use App\Services\Payment\CallbackAuthenticityVerifier;
use App\Services\Payment\PaymentManager;
use Illuminate\Log\Logger;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class PaymentCallbackController extends Controller
{
    public function __construct(
        private readonly CallbackAuthenticityVerifier $callbackAuthenticityVerifier,
        private readonly OrderProvider $orderProvider,
        private readonly OrderManager $orderManager,
        private readonly PaymentManager $paymentManager,
        private readonly Logger $logger,
    ) {
    }

    /**
     * @throws ValidationException
     */
    public function __invoke(CallbackRequest $request): Response
    {
        if (!$this->isAuthorized($request)) {
            return new Response(status: Response::HTTP_UNAUTHORIZED);
        }

        $paymentCallbackPayload = $this->validateRequest($request);

        $order = $this->findOrderByReferenceId($paymentCallbackPayload->getReferenceId());
        if ($order === null) {
            return new Response(status: Response::HTTP_NOT_FOUND);
        }

        $payment = $this->paymentManager->createOrUpdateFromCallback($order, $paymentCallbackPayload);
        $this->orderManager->updateOrderStatusByPayment($order, $payment->getStatus());

        $order->save();
        $payment->save();

        return new Response(status: Response::HTTP_OK);
    }

    private function findOrderByReferenceId(string $referenceId): Order|null
    {
        $order = $this->orderProvider->findByUuid($referenceId);

        if ($order === null) {
            $this->logger->error('Cannot find order by payment reference id!', ['referenceId' => $referenceId]);
        }

        return $order;
    }

    /**
     * @throws ValidationException
     */
    private function validateRequest(CallbackRequest $request): PaymentCallbackPayload
    {
        try {
            $request->validate($request->getValidationRules());

            return $request->getCallbackPayload();

        } catch (ValidationException $exception) {
            $this->logger->error(
                'An invalid payment service callback request received!', ['content' => $request->getContent()]
            );

            throw $exception;
        }
    }

    private function isAuthorized(CallbackRequest $request): bool
    {
        $signatureHeader = $request->headers->get(CallbackAuthenticityVerifier::CALLBACK_REQUEST_HEADER_NAME_SIGNATURE);
        if ($signatureHeader === null || trim($signatureHeader) === '') {
            return false;
        }

        if ($request->ip() === null || !is_string($request->getContent())) {
            return false;
        }

        $isVerified = $this->callbackAuthenticityVerifier->verifyRequest(
            $request->ip(),
            $request->getContent(),
            $signatureHeader,
        );

        if (!$isVerified) {
            $this->logger->error(
                'An unathorized payment service callback request received!',
                [
                    'ip' => $request->ip(),
                    'headers' => $request->headers->all(),
                    'content' => $request->getContent(),
                ]
            );
        }

        return $isVerified;
    }
}
