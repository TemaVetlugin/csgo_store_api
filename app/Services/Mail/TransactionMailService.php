<?php

declare(strict_types=1);

namespace App\Services\Mail;

use App\Mail\FailedTransactionNotificationMail;
use App\Mail\SuccessTransactionNotificationMail;
use App\Models\Transaction;
use App\Models\User;
use App\Services\Transaction\TransactionStatusReasonProvider;
use Illuminate\Mail\MailManager;
use InvalidArgumentException;

class TransactionMailService
{
    public function __construct(
        private readonly MailManager $mailManager,
        private readonly TransactionStatusReasonProvider $transactionStatusReasonProvider,
    ) {
    }

    public function sendFailedTransactionMailToUser(User $user, Transaction $transaction): void
    {
        if ($user->getEmail() === null || $user->getEmailVerifiedAt() === null) {
            throw new InvalidArgumentException(
                'Cannot send email to a user without verified email address. User ID = ' . $user->getId(),
            );
        }

        $mail = new FailedTransactionNotificationMail(
            transactionStatus: $transaction->getStatus(),
            productName: $transaction->getProductName(),
            failureReason: $this->transactionStatusReasonProvider->getForStatus($transaction->getStatus()),
        );

        $this->mailManager
            ->to($user->getEmail())
            ->send($mail)
        ;
    }

    public function sendSuccessTransactionMailToUser(User $user, Transaction $transaction): void
    {
        if ($user->getEmail() === null || $user->getEmailVerifiedAt() === null) {
            throw new InvalidArgumentException(
                'Cannot send email to a user without verified email address. User ID = ' . $user->getId(),
            );
        }

        $mail = new SuccessTransactionNotificationMail(
            productName: $transaction->getProductName(),
        );

        $this->mailManager
            ->to($user->getEmail())
            ->send($mail)
        ;
    }
}
