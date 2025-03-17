<?php

declare(strict_types=1);

namespace App\Mail\MaintainerNotification;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InsufficientFundsOnMarketplaceAccountMail extends Mailable
{
    private const MAIL_SUBJECT = 'CS:GO Store: Warning! Not enough funds on the marketplace account to buy a product.';

    use Queueable, SerializesModels;

    public function __construct(
        private readonly string $userEmail,
        private readonly array $productNames,
        private readonly float $orderAmount,
        private readonly float $actualMarketplaceBalance,
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
            markdown: 'mails.maintainer-notification.insufficient-funds-on-marketplace-account',
            with: [
                'userEmail' => $this->userEmail,
                'productNames' => $this->productNames,
                'orderAmount' => $this->orderAmount,
                'actualMarketplaceBalance' => $this->actualMarketplaceBalance,
            ],
        );
    }
}
