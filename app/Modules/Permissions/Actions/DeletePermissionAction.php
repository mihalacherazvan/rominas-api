<?php

declare(strict_types=1);

namespace Rominas\Permissions\Actions;

use Rominas\Permissions\Model\Permission;

class DeletePermissionAction
{
    public function execute(Permission $permission): ?bool
    {
        return $permission->delete();
    }
}
