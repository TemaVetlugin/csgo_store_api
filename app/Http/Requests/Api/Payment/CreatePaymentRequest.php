<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Payment;

use Illuminate\Foundation\Http\FormRequest;

class CreatePaymentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'uuid' => ['required', 'string'],
            'address' => ['string', 'nullable'],
            'city' => ['string', 'nullable'],
            'country' => ['string', 'nullable'],
            'countryIso' => ['string', 'nullable'],
            'firstName' => ['string', 'nullable'],
            'lastName' => ['string', 'nullable'],
            'notes' => ['string', 'nullable'],
            'phone' => ['string', 'nullable'],
        ];
    }

    public function getUuid(): string
    {
        $validatedParameters = $this->validated();

        return $validatedParameters['uuid'];
    }

    public function getOrderData(): array
    {
        $validatedParameters = $this->validated();

        return [
            'address' => $validatedParameters['address'],
            'city' => $validatedParameters['city'],
            'country' => $validatedParameters['country'],
            'countryIso' => $validatedParameters['countryIso'],
            'firstName' => $validatedParameters['firstName'],
            'lastName' => $validatedParameters['lastName'],
            'notes' => $validatedParameters['notes'],
            'phone' => $validatedParameters['phone'],
        ];
    }
}
