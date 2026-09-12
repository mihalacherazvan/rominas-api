<?php

declare(strict_types=1);

namespace Rominas\Categories\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Rominas\Categories\Model\Category;
use Rominas\Users\Model\User;

class CategoryPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('categories');
    }

    public function view(User $user, Category $model): bool
    {
        return $user->can('categories');
    }

    public function create(User $user): bool
    {
        return $user->can('categories');
    }

    public function update(User $user, Category $model): bool
    {
        return $user->can('categories');
    }

    public function delete(User $user, Category $model): bool
    {
        return $user->can('categories');
    }
}
