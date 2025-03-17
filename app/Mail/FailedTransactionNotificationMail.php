<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Enum\TransactionStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FailedTransactionNotificationMail extends Mailable
{
    private const MAIL_SUBJECT = 'CS:GO Store: failed to buy a product';

    use Queueable, SerializesModels;

    public function __construct(
        private readonly TransactionStatus $transactionStatus,
        private readonly string $productName,
        private readonly string $failureReason,
    ) {
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(subject: self::MAIL_SUBJECT);
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mails.failed-transaction-notification',
            with: [
                'status' => $this->transactionStatus->toString(),
                'productName' => $this->productName,
                'reason' => $this->failureReason,
            ],
        );
    }
}
