<?php

declare(strict_types=1);

namespace Rominas\Roles\Actions;

use Rominas\Roles\Model\Role;

class DeleteRoleAction
{
    public function execute(Role $role): ?bool
    {
        return $role->delete();
    }
}
