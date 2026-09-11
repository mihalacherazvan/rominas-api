<?php

declare(strict_types=1);

namespace Rominas\Users\DataTransferObjects;

class UserData
{
    /**
     * @param  int[]  $associatedRoleIds
     */
    public function __construct(
        public string $name,
        public string $email,
        public array $associatedRoleIds,
        public ?string $password = null,
        public ?string $newPassword = null,
    ) {}
}
