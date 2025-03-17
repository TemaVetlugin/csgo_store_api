<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Payment;

use App\Entity\DTO\Payment\GetStatusPayload;
use Illuminate\Foundation\Http\FormRequest;

class GetStatusRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'uuid' => ['required'],
        ];
    }
    public function getUuid(): string
    {
        $validatedParameters = $this->validated();

        return $validatedParameters['uuid'];
    }
}
