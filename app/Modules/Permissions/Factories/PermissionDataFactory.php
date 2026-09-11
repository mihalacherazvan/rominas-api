<?php

declare(strict_types=1);

namespace Rominas\Permissions\Factories;

use Rominas\Permissions\DataTransferObjects\PermissionData;
use Rominas\Permissions\Requests\CreatePermissionRequest;
use Rominas\Permissions\Requests\UpdatePermissionRequest;

class PermissionDataFactory
{
    public static function fromRequest(
        CreatePermissionRequest|UpdatePermissionRequest $permissionRequest,
    ): PermissionData {
        $validatedData = $permissionRequest->validated();

        return new PermissionData(
            name: $validatedData['name'],
            associatedRoleIds: array_map(
                static fn(array $role) => $role['id'],
                $validatedData['associatedRoles'] ?? [],
            ),
        );
    }
}
