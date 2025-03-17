<?php

declare(strict_types=1);

namespace App\Models\Enum;

enum PaymentStatus: string
{
    case New = 'new';
    case Created = 'created';
    case Error = 'error';
    case Expired = 'expired';
    case Prepared = 'prepared';
    case Authorized = 'authorized';
    case Charged = 'charged';
    case Reversed = 'reversed';
    case Refunded = 'refunded';
    case Rejected = 'rejected';
    case Fraud = 'fraud';
    case Declined = 'declined';
    case Chargedback = 'chargedback';
    case Credited = 'credited';
    case ProcessPending = 'process_pending';
    case Processed = 'processed';
    case ProcessFailed = 'process_failed';
    case RefundPending = 'refund_pending';
    case PartiallyRefunded = 'partially_refunded';
    case RefundFailed = 'refund_failed';
    case ChargedBack = 'charged_back';
    case PartiallyChargedBack = 'partially_charged_back';
    case Success = 'success';
    case Failure = 'failure';
    case Pending = 'pending';

    public function toString(): string
    {
        return $this->value;
    }
}
