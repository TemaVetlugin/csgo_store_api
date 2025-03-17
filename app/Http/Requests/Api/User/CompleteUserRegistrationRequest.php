<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\User;

use App\Entity\DTO\Payload\CompleteUserRegistrationPayload;
use App\Rules\ValidSteamTradeLinkRule;
use Illuminate\Foundation\Http\FormRequest;

class CompleteUserRegistrationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email:rfc,strict', 'unique:users,email'],
            'steam_trade_link' => ['required', 'string', new ValidSteamTradeLinkRule()],
        ];
    }

    public function getValidatedPayload(): CompleteUserRegistrationPayload
    {
        $validatedPayload = $this->validated();

        return new CompleteUserRegistrationPayload(
            $validatedPayload['email'],
            $validatedPayload['steam_trade_link'],
        );
    }
}
