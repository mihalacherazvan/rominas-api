<?php

declare(strict_types=1);

namespace Rominas\Delivery;

class EmailContact
{
    public function __construct(
        public string $email,
        public ?string $name = null,
    ) {}
}
