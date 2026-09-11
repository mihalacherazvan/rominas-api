<?php

declare(strict_types=1);

namespace Rominas\Roles\Factories;

use Rominas\Roles\DataTransferObjects\RoleData;
use Rominas\Roles\Requests\CreateRoleRequest;
use Rominas\Roles\Requests\UpdateRoleRequest;

class RoleDataFactory
{
    public static function fromRequest(CreateRoleRequest|UpdateRoleRequest $roleRequest): RoleData
    {
        $validatedData = $roleRequest->validated();

        return new RoleData(
            name: $validatedData['name'],
            associatedPermissionIds: array_map(
                static fn(array $permission) => $permission['id'],
                $validatedData['associatedPermissions'],
            ),
        );
    }
}
