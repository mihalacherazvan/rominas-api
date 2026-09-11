<?php

declare(strict_types=1);

namespace Rominas\Permissions\Actions;

use Rominas\Permissions\DataTransferObjects\PermissionData;
use Rominas\Permissions\Model\Permission;
use Rominas\Roles\Model\Role;

class CreatePermissionAction
{
    public function execute(PermissionData $permissionDto): Permission
    {
        /** @var Permission $permission */
        $permission = Permission::create([
            'name' => $permissionDto->name,
            'guard_name' => $permissionDto->guard_name,
        ]);

        $permission->syncRoles(Role::find($permissionDto->associatedRoleIds));

        return $permission;
    }
}
