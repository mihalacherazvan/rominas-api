<?php

declare(strict_types=1);

namespace Rominas\Delivery;

class MailPayload implements DeliveryPayloadInterface
{
    /**
     * @param  list<string>  $recipients
     * @param  array<int, mixed>  $attachments  URLs or ['path' => ..., 'name' => ...] entries
     * @param  list<EmailContact>  $cc
     */
    public function __construct(
        public EmailContact $sender,
        public array $recipients,
        public string $subject,
        public string $body,
        public array $attachments = [],
        public array $cc = [],
    ) {}
}
