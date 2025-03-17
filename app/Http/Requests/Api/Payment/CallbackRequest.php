<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Payment;

use App\Entity\DTO\Payment\PaymentCallbackPayload;
use App\Models\Enum\PaymentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CallbackRequest extends FormRequest
{
    public function getValidationRules(): array
    {
        return [
            'data' => [
                'id' => ['required', 'string', 'min:1'],
                'attributes' => [
                    'status' => ['required', 'string', Rule::enum(PaymentStatus::class)],
                    'resolution' => ['sometimes', 'required', 'string', 'min:1'],
                    'amount' => ['sometimes', 'required', 'numeric'],
                    'fee' => ['sometimes', 'required', 'numeric'],
                    'currency' => ['sometimes', 'required', 'string', 'min:1'],
                    'reference_id' => ['required', 'string', 'min:1'],
                    'updated' => ['required', 'numeric'],
                    'payload' => [
                        'token' => ['sometimes', 'required', 'string', 'min:1'],
                    ],
                ],
                'relationships' => [
                    'payment-service' => [
                        'data' => [
                            'id' => ['sometimes', 'required', 'string', 'min:1'],
                        ],
                    ],
                ],
            ],
        ];
    }

    public function getCallbackPayload(): PaymentCallbackPayload
    {
        $callbackParameters = $this->all();

        $callbackPayload = new PaymentCallbackPayload(
            paymentId: $callbackParameters['data']['id'],
            referenceId: $callbackParameters['data']['attributes']['reference_id'],
            status: PaymentStatus::from($callbackParameters['data']['attributes']['status']),
            timestamp: (int) $callbackParameters['data']['attributes']['updated'],
        );

        if (isset($callbackParameters['data']['attributes']['resolution'])) {
            $callbackPayload->setResolution($callbackParameters['data']['attributes']['resolution']);
        }

        if (isset($callbackParameters['data']['relationships']['payment-service']['data']['id'])) {
            $callbackPayload->setPaymentMethod($callbackParameters['data']['relationships']['payment-service']['data']['id']);
        }

        if (isset($callbackParameters['data']['attributes']['amount'])) {
            $callbackPayload->setAmount((float) $callbackParameters['data']['attributes']['amount']);
        }

        if (isset($callbackParameters['data']['attributes']['fee'])) {
            $callbackPayload->setFee((float) $callbackParameters['data']['attributes']['fee']);
        }

        if (isset($callbackParameters['data']['attributes']['currency'])) {
            $callbackPayload->setCurrency(strtolower($callbackParameters['data']['attributes']['currency']));
        }

        if (isset($callbackParameters['data']['attributes']['payload']['token'])) {
            $callbackPayload->setToken($callbackParameters['data']['attributes']['payload']['token']);
        }

        return $callbackPayload;
    }
}
