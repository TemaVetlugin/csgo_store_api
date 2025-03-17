<?php

declare(strict_types=1);

namespace App\Mail;

use App\Entity\DTO\Payload\ContactUsPayload;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactUsMail extends Mailable
{
    private const MAIL_SUBJECT = 'CS:GO Store: Contact Us message';

    use Queueable, SerializesModels;

    public function __construct(private readonly ContactUsPayload $contactUsPayload)
    {
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            replyTo: $this->contactUsPayload->getUserEmail(),
            subject: self::MAIL_SUBJECT,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mails.contact-us',
            with: [
                'userEmail' => $this->contactUsPayload->getUserEmail(),
                'content' => $this->contactUsPayload->getMessage(),
            ],
        );
    }
}
