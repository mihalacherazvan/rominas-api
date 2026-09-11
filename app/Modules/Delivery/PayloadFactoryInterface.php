<?php

declare(strict_types=1);

namespace Rominas\Delivery;

interface PayloadFactoryInterface
{
    public function create(): DeliveryPayloadInterface;
}
