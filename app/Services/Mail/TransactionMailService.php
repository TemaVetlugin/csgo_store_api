<?php

declare(strict_types=1);

namespace App\Services\Mail;

use App\Mail\AdminOrderNotificationMail;
use App\Mail\AdminTransactionNotificationMail;
use App\Mail\FailedTransactionNotificationMail;
use App\Mail\SuccessTransactionNotificationMail;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
use App\Services\Transaction\TransactionStatusReasonProvider;
use Illuminate\Mail\MailManager;
use Illuminate\Support\Facades\Config;
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

    public function sendOrderMailToAdmin(Order $order): void
    {
        $email = Config::get('maintenance.email_addresses');

        $mail = new AdminTransactionNotificationMail(
            productPrice: (string)$order->getTotalPrice(),
            productStatus: $order->getStatus()->value,
        );

        $this->mailManager
            ->to($email)
            ->send($mail)
        ;
    }

    public function sendTransactionMailToAdmin(Transaction $transaction): void
    {
        $email = Config::get('maintenance.email_addresses');

        $order = $transaction->getOrder();

        $mail = new AdminOrderNotificationMail(
            productName: $transaction->getProductName(),
            productPrice: (string)$order->getTotalPrice(),
            reason: $this->transactionStatusReasonProvider->getForStatus($transaction->getStatus()),
        );

        $this->mailManager
            ->to($email)
            ->send($mail)
        ;
    }
}
