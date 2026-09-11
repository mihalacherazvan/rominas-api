<?php

declare(strict_types=1);

namespace Rominas\Roles\Actions;

use Rominas\Roles\DataTransferObjects\RoleData;
use Rominas\Roles\Model\Role;
use Spatie\Permission\Models\Permission;

class UpdateRoleAction
{
    public function execute(Role $role, RoleData $roleData): Role
    {
        $role->name = $roleData->name;

        $role->permissions()->sync(Permission::find($roleData->associatedPermissionIds));

        $role->save();

        return $role;
    }
}
