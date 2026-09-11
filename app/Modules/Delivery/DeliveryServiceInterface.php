<?php

declare(strict_types=1);

namespace Rominas\Delivery;

interface DeliveryServiceInterface
{
    public function send(DeliveryPayloadInterface $payload): void;
}
