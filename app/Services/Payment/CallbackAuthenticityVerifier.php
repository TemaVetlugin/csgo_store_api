<?php

declare(strict_types=1);

namespace App\Services\Payment;

class CallbackAuthenticityVerifier
{
    public const CALLBACK_REQUEST_HEADER_NAME_SIGNATURE = 'x-signature';

    public function __construct(
        private readonly string $paymentServicePrivateKey,
        private readonly array $ipWhitelist,
    ) {
    }

    public function verifyRequest(string $ip, string $payloadContent, string $signature): bool
    {
        if (count($this->ipWhitelist) > 0 && !in_array($ip, $this->ipWhitelist, true)) {
            return false;
        }

        $expectedSignaturePayload = $this->paymentServicePrivateKey . $payloadContent . $this->paymentServicePrivateKey;
        $expectedSignature = base64_encode(sha1($expectedSignaturePayload, true));

        return $signature === $expectedSignature;
    }
}
