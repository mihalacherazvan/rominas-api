<?php

declare(strict_types=1);

namespace Rominas\Delivery\Brevo;

use Rominas\Delivery\EmailContact;
use Rominas\Delivery\MailPayload;

class BrevoMailPayload extends MailPayload
{
    /**
     * @param  list<string>  $recipients
     * @param  array<int, mixed>  $attachments  URLs or ['path' => ..., 'name' => ...] entries
     * @param  list<EmailContact>  $cc
     * @param  list<string>  $tags
     */
    public function __construct(
        public EmailContact $sender,
        public array $recipients,
        public string $subject,
        public string $body,
        public array $attachments = [],
        public array $cc = [],
        public array $tags = [],
    ) {
        parent::__construct(
            sender: $sender,
            recipients: $recipients,
            subject: $subject,
            body: $body,
            attachments: $attachments,
            cc: $cc,
        );
    }
}
