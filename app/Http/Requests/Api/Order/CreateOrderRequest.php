<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Order;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'product_ids' => ['required', 'required', 'array', 'min:1'],
            'product_ids.*' => ['required', 'string', 'numeric', 'distinct', 'min:1'],
        ];
    }

    public function getProductIds(): array
    {
        $validatedParameters = $this->validated();

        return $validatedParameters['product_ids'];
    }

}
