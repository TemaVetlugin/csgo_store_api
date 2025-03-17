<?php

declare(strict_types=1);

namespace App\Models\Enum;

enum TransactionType: string
{
    case Buy = 'buy';
    case Sell = 'sell';

    public static function getAllowedValues(): array
    {
        return array_map(static fn (self $case) => $case->toString(), self::cases());
    }

    public function toString(): string
    {
        return $this->value;
    }
}
