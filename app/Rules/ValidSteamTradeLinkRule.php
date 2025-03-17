<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidSteamTradeLinkRule implements ValidationRule
{
    public const REGEXP_VALID_STEAM_TRADE_LINK = '/http[s]{0,1}:\/\/steamcommunity\.com\/tradeoffer\/new\/\?partner=[0-9]{4,11}&token=[a-zA-Z0-9_-]{8}$/';

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!preg_match(self::REGEXP_VALID_STEAM_TRADE_LINK, $value)) {
            $fail('Steam trade link has invalid format.');
        }
    }
}
