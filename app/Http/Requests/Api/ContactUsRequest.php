<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Entity\DTO\Payload\ContactUsPayload;
use Illuminate\Foundation\Http\FormRequest;

class ContactUsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'user_email' => ['required', 'string', 'email:rfc,strict'],
            'message' => ['required', 'string', 'min:1', 'max:3000'],
        ];
    }

    public function getValidatedPayload(): ContactUsPayload
    {
        $validated = $this->validated();

        return new ContactUsPayload(
            $validated['user_email'],
            $validated['message'],
        );
    }
}
