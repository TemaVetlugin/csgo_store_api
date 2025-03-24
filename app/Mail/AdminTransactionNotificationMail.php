<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Enum\TransactionStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminTransactionNotificationMail extends Mailable
{
    private const MAIL_SUBJECT = 'CS:GO Store: New order';

    use Queueable, SerializesModels;

    public function __construct(
        private readonly string $productPrice,
        private readonly string $productStatus,
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
            markdown: 'mails.success-transaction-admin-notification',
            with: [
                'productPrice' => $this->productPrice,
                'productStatus' => $this->productStatus,
            ],
        );
    }
}
