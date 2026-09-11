<?php

declare(strict_types=1);

namespace Rominas\Delivery\SMTP;

use Rominas\Delivery\DeliveryPayloadInterface;
use Rominas\Delivery\EmailContact;
use Rominas\Delivery\PayloadFactoryInterface;

/**
 * Generic, domain-neutral example payload factory.
 *
 * Demonstrates the action => factory => service pipeline without depending on any
 * domain model, Blade view, or attachment builder. Replace with real factories as
 * Door grows its own deliverable domain (e.g. account verification, billing receipts).
 */
class SmtpExampleNotificationPayloadFactory implements PayloadFactoryInterface
{
    public function __construct(
        private readonly string $recipientEmail,
        private readonly string $subject,
        private readonly string $body,
    ) {}

    public function create(): DeliveryPayloadInterface
    {
        return new SmtpMailPayload(
            sender: new EmailContact(
                email: config('mail.from.address'),
                name: config('mail.from.name'),
            ),
            recipients: [$this->recipientEmail],
            subject: $this->subject,
            body: $this->body,
        );
    }
}
