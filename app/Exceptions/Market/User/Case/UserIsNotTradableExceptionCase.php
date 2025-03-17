<?php

declare(strict_types=1);

namespace App\Exceptions\Market\User\Case;

enum UserIsNotTradableExceptionCase: string
{
    case SteamTradeLinkIsNotSet = 'steam_trade_link_is_not_set';
    case EmailIsNotVerified = 'email_is_not_verified';

    public function toString(): string
    {
        return $this->value;
    }
}
