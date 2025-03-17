<?php

declare(strict_types=1);

namespace App\Services\Steam;

use App\Entity\DTO\Steam\SteamTradeLinkPayload;
use InvalidArgumentException;

class SteamTradeLinkParser
{
    private const REGEXP_STEAM_TRADE_LINK_PATTERN = '~https://steamcommunity.com/tradeoffer/new/\?partner=(\d+)&token=(\w+)~';

    public function parsePayload(string $steamTradeLink): SteamTradeLinkPayload
    {
        preg_match(self::REGEXP_STEAM_TRADE_LINK_PATTERN, $steamTradeLink, $matches);

        if (count($matches) < 3) {
            throw new InvalidArgumentException(
                sprintf('Cannot parse "partner" and "token" payload from the steam trade link: %s', $steamTradeLink)
            );
        }

        return new SteamTradeLinkPayload($matches[1], $matches[2]);
    }
}
