<?php

declare(strict_types=1);

namespace App\Models\Enum;

enum OrderStatus: string
{
    case Created = 'created';
    case Pending = 'pending';
    case Processed = 'processed';
    case Failed = 'failed';

    public function toString(): string
    {
        return $this->value;
    }
}
