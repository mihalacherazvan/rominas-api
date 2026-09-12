<?php

declare(strict_types=1);

namespace Rominas\Catalog\Artist\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Rominas\Catalog\Artist\Model\Artist;
use Rominas\Users\Model\User;

class ArtistPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('artists');
    }

    public function view(User $user, Artist $model): bool
    {
        return $user->can('artists');
    }

    public function create(User $user): bool
    {
        return $user->can('artists');
    }

    public function update(User $user, Artist $model): bool
    {
        return $user->can('artists');
    }

    public function delete(User $user, Artist $model): bool
    {
        return $user->can('artists');
    }
}
