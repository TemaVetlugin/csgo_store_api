<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\User;

use App\Entity\DTO\Payload\UpdateUserPayload;
use App\Rules\ValidSteamTradeLinkRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCurrentUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => ['sometimes', 'required', 'email:rfc,strict', 'unique:users,email'],
            'steam_trade_link' => ['sometimes', 'required', 'string', new ValidSteamTradeLinkRule()],
        ];
    }

    public function getValidatedPayload(): UpdateUserPayload
    {
        $validatedPayload = $this->validated();

        $updateUserPayload = new UpdateUserPayload();

        if (isset($validatedPayload['email'])) {
            $updateUserPayload->setEmail($validatedPayload['email']);
        }

        if (isset($validatedPayload['steam_trade_link'])) {
            $updateUserPayload->setSteamTradeLink($validatedPayload['steam_trade_link']);
        }

        return $updateUserPayload;
    }
}
