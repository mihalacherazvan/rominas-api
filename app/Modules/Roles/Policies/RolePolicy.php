<?php

declare(strict_types=1);

namespace Rominas\Roles\Policies;

use Rominas\Roles\Model\Role;
use Rominas\Users\Model\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Role $model): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->can('roles');
    }

    public function update(User $user, Role $model): bool
    {
        return $user->can('roles');
    }

    public function delete(User $user, Role $model): bool
    {
        return $user->can('roles');
    }
}
