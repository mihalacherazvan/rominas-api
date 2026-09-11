<?php

declare(strict_types=1);

namespace Rominas\Delivery;

interface MailServiceInterface
{
    public function send(MailPayload $payload): void;
}
