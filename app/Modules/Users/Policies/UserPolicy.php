<?php

declare(strict_types=1);

namespace Rominas\Users\Policies;

use Rominas\Roles\Model\Role;
use Rominas\Users\Model\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        $roles = Role::query()->visibleToUser($user)->get();

        if ($roles->isEmpty()) {
            return false;
        }

        return $user->can('users.view.' . $roles->implode('name', ','));
    }

    public function view(User $user, User $model): bool
    {
        if ($user->id === $model->id) {
            return true;
        }

        $roleName = $model->getRoleNames()->first();

        return is_string($roleName) && $user->can('users.view.' . $roleName);
    }

    public function create(User $user): bool
    {
        $roles = Role::query()->creatableByUser($user)->get();

        if ($roles->isEmpty()) {
            return false;
        }

        return $user->can('users.create.' . $roles->implode('name', ','));
    }

    public function update(User $user, User $model): bool
    {
        $roleName = $model->getRoleNames()->first();

        if (! is_string($roleName) || ! $user->can('users.update.' . $roleName)) {
            return false;
        }

        return User::query()->updatableByUser($user)->whereKey($model->id)->exists();
    }

    public function updateProfile(User $user, User $model): bool
    {
        return $user->id === $model->id;
    }

    public function delete(User $user, User $model): bool
    {
        $roleName = $model->getRoleNames()->first();

        if (! is_string($roleName) || ! $user->can('users.delete.' . $roleName)) {
            return false;
        }

        return User::query()->deletableByUser($user)->whereKey($model->id)->exists();
    }
}
