<?php

declare(strict_types=1);

namespace Rominas\Catalog\Album\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Rominas\Catalog\Album\Model\Album;
use Rominas\Users\Model\User;

class AlbumPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('albums');
    }

    public function view(User $user, Album $model): bool
    {
        return $user->can('albums');
    }

    public function create(User $user): bool
    {
        return $user->can('albums');
    }

    public function update(User $user, Album $model): bool
    {
        return $user->can('albums');
    }

    public function delete(User $user, Album $model): bool
    {
        return $user->can('albums');
    }
}
