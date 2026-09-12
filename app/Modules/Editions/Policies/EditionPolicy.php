<?php

declare(strict_types=1);

namespace Rominas\Editions\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Rominas\Editions\Model\Edition;
use Rominas\Users\Model\User;

class EditionPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('editions');
    }

    public function view(User $user, Edition $model): bool
    {
        return $user->can('editions');
    }

    public function create(User $user): bool
    {
        return $user->can('editions');
    }

    public function update(User $user, Edition $model): bool
    {
        return $user->can('editions');
    }

    public function delete(User $user, Edition $model): bool
    {
        return $user->can('editions');
    }
}
