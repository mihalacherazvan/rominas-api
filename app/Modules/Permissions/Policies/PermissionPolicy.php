<?php

declare(strict_types=1);

namespace Rominas\Permissions\Policies;

use Rominas\Permissions\Model\Permission;
use Rominas\Users\Model\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PermissionPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('permissions');
    }

    public function view(User $user, Permission $model): bool
    {
        return $user->can('permissions');
    }

    public function create(User $user): bool
    {
        return $user->can('permissions');
    }

    public function update(User $user, Permission $model): bool
    {
        return $user->can('permissions');
    }

    public function delete(User $user, Permission $model): bool
    {
        return $user->can('permissions');
    }
}
