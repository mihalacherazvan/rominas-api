<?php

declare(strict_types=1);

namespace Rominas\Roles\Actions;

use Rominas\Roles\DataTransferObjects\RoleData;
use Rominas\Roles\Model\Role;
use Spatie\Permission\Models\Permission;

class CreateRoleAction
{
    public function execute(RoleData $roleData): Role
    {
        /** @var Role $role */
        $role = Role::create([
            'name' => $roleData->name,
            'guard_name' => $roleData->guard_name,
        ]);

        $role->permissions()->sync(Permission::find($roleData->associatedPermissionIds));

        $role->refresh();

        return $role;
    }
}
