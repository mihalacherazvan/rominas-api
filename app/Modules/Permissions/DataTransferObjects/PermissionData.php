<?php

declare(strict_types=1);

namespace Rominas\Permissions\DataTransferObjects;

class PermissionData
{
    /**
     * @param  int[]  $associatedRoleIds
     */
    public function __construct(
        public string $name,
        public array $associatedRoleIds = [],
        public string $guard_name = 'web',
    ) {}
}
