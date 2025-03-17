<?php

declare(strict_types=1);

namespace App\Models\Enum;

/**
 * @see https://skinsback.com/docs/market_buy (offer_status)
 */
enum TransactionStatus: string
{
    // Order is already created, payment is not yet
    case Pending = 'pending';
    // Offer Statuses
    case Accepted = 'accepted';
    case CreatingTrade = 'creating_trade';
    case WaitingAccept = 'waiting_accept';
    case Canceled = 'canceled';
    case Timeout = 'timeout';
    case InvalidTradeToken = 'invalid_trade_token';
    case UserNotTradable = 'user_not_tradable';
    case TradeCreateError = 'trade_create_error';

    // Buy Product API errors
    case MarketDisabled = 'market_disabled';
    case InsufficientFunds = 'insufficient_funds';
    case ProductUnavailable = 'skin_unavailable';
    case ProductNotFoundAtSpecifiedPrice = 'skins_not_found_at_specified_price';
    case InvalidTradeLinkPartner = 'invalid_partner_value';

    // Standard Market API errors
    case MarketIsTemporaryUnavailable = 'request_limit_reached';
    case UnknownError = 'unknown_error';

    public static function getAllowedValues(): array
    {
        return array_map(static fn (self $case) => $case->toString(), self::cases());
    }

    public function toString(): string
    {
        return $this->value;
    }
}
