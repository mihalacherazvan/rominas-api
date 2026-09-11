<?php

declare(strict_types=1);

namespace Rominas\Delivery\SMTP;

use Rominas\Delivery\DeliveryPayloadInterface;
use Rominas\Delivery\EmailContact;
use Rominas\Delivery\PayloadFactoryInterface;
use Illuminate\Support\Str;

/**
 * Renders the registration magic-link email for an address (no Client exists yet). The link carries
 * the raw token and email to the frontend registration screen, which confirms them and creates the
 * account on first login.
 */
class SmtpMagicLinkEmailPayloadFactory implements PayloadFactoryInterface
{
    public function __construct(
        private readonly string $email,
        private readonly string $token,
    ) {}

    public function create(): DeliveryPayloadInterface
    {
        $url = rtrim((string) config('services.frontend.url'), '/')
            . '/onboarding/creeaza-cont?token=' . $this->token
            . '&email=' . rawurlencode($this->email);

        $body = view('emails.magic-link', [
            'name' => Str::before($this->email, '@'),
            'url' => $url,
        ])->render();

        return new SmtpMailPayload(
            sender: new EmailContact(
                email: config('mail.from.address'),
                name: config('mail.from.name'),
            ),
            recipients: [$this->email],
            subject: 'Confirmă-ți adresa de email Door',
            body: $body,
        );
    }
}
