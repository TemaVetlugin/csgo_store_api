<?php

declare(strict_types=1);

namespace App\Services\Mail;

use App\Entity\DTO\Payload\ContactUsPayload;
use App\Mail\ContactUsMail;
use Illuminate\Mail\MailManager;

class ContactUsMailService
{
    public function __construct(
        private readonly MailManager $mailManager,
        private readonly array $maintainerEmailAddresses,
    ) {
    }

    public function sendToAdministrator(ContactUsPayload $contactUsPayload): void
    {
        $this->mailManager
            ->to($this->maintainerEmailAddresses)
            ->send(new ContactUsMail($contactUsPayload))
        ;
    }
}
