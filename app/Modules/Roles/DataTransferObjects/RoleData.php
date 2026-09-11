<?php

declare(strict_types=1);

namespace Rominas\Roles\DataTransferObjects;

class RoleData
{
    /**
     * @param  int[]  $associatedPermissionIds
     */
    public function __construct(
        public string $name,
        public array $associatedPermissionIds,
        public string $guard_name = 'web',
    ) {}
}
