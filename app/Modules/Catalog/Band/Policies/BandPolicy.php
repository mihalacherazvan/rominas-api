<?php

declare(strict_types=1);

namespace Rominas\Catalog\Band\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Rominas\Catalog\Band\Model\Band;
use Rominas\Users\Model\User;

class BandPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('bands');
    }

    public function view(User $user, Band $model): bool
    {
        return $user->can('bands');
    }

    public function create(User $user): bool
    {
        return $user->can('bands');
    }

    public function update(User $user, Band $model): bool
    {
        return $user->can('bands');
    }

    public function delete(User $user, Band $model): bool
    {
        return $user->can('bands');
    }
}
