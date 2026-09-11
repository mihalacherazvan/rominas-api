<?php

declare(strict_types=1);

namespace Rominas\Delivery\Brevo;

use Brevo\Brevo;
use Brevo\TransactionalEmails\Requests\SendTransacEmailRequest;
use Brevo\TransactionalEmails\Types\SendTransacEmailRequestAttachmentItem;
use Brevo\TransactionalEmails\Types\SendTransacEmailRequestCcItem;
use Brevo\TransactionalEmails\Types\SendTransacEmailRequestSender;
use Brevo\TransactionalEmails\Types\SendTransacEmailRequestToItem;
use Rominas\Delivery\DeliveryPayloadInterface;
use Rominas\Delivery\DeliveryServiceInterface;
use Rominas\Delivery\EmailContact;
use Rominas\Delivery\MailServiceInterface;
use InvalidArgumentException;

class BrevoMailService implements DeliveryServiceInterface, MailServiceInterface
{
    public function __construct(
        public string $fromEmail,
        public string $fromName,
        public string $apiKey,
    ) {}

    public function send(DeliveryPayloadInterface $payload): void
    {
        if (! ($payload instanceof BrevoMailPayload)) {
            throw new InvalidArgumentException('Invalid payload');
        }

        $attachments = collect($payload->attachments)->map(function ($attachment) {
            if (filter_var($attachment, FILTER_VALIDATE_URL)) {
                return new SendTransacEmailRequestAttachmentItem([
                    'url' => $attachment,
                ]);
            }

            return new SendTransacEmailRequestAttachmentItem([
                'content' => base64_encode(file_get_contents($attachment['path'])),
                'name' => $attachment['name'],
            ]);
        })->all();

        $cc = collect($payload->cc)->map(fn(EmailContact $contact) => new SendTransacEmailRequestCcItem([
            'email' => $contact->email,
            'name' => $contact->name,
        ]))->all();

        $to = collect($payload->recipients)->map(fn(string $recipient) => new SendTransacEmailRequestToItem([
            'email' => $recipient,
        ]))->all();

        $brevo = new Brevo(apiKey: $this->apiKey);

        $brevo->transactionalEmails->sendTransacEmail(new SendTransacEmailRequest([
            'sender' => new SendTransacEmailRequestSender([
                'email' => $payload->sender->email,
                'name' => $payload->sender->name,
            ]),
            'to' => $to,
            'cc' => empty($cc) ? null : $cc,
            'subject' => $payload->subject,
            'htmlContent' => $payload->body,
            'attachment' => empty($attachments) ? null : $attachments,
            'tags' => $payload->tags,
        ]));
    }
}
