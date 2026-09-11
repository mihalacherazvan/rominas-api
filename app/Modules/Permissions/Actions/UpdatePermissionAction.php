<?php

declare(strict_types=1);

namespace Rominas\Permissions\Actions;

use Rominas\Permissions\DataTransferObjects\PermissionData;
use Rominas\Permissions\Model\Permission;
use Rominas\Roles\Model\Role;

class UpdatePermissionAction
{
    public function execute(Permission $permission, PermissionData $updatedPermission): Permission
    {
        $permission->name = $updatedPermission->name;

        $permission->syncRoles(Role::find($updatedPermission->associatedRoleIds));

        $permission->save();

        return $permission;
    }
}
