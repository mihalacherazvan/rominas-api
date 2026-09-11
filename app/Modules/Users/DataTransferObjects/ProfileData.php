<?php

declare(strict_types=1);

namespace Rominas\Users\DataTransferObjects;

class ProfileData
{
    public function __construct(
        public string $name,
        public string $email,
        public ?string $newPassword = null,
    ) {}
}
