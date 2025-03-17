<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Order;

use Illuminate\Foundation\Http\FormRequest;

class GetOrderPriceRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'uuid' => ['required', 'string'],
        ];
    }

    public function getUuid(): string
    {
        $validatedParameters = $this->validated();

        return $validatedParameters['uuid'];
    }

}
