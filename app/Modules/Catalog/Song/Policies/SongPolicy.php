<?php

declare(strict_types=1);

namespace Rominas\Catalog\Song\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Rominas\Catalog\Song\Model\Song;
use Rominas\Users\Model\User;

class SongPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('songs');
    }

    public function view(User $user, Song $model): bool
    {
        return $user->can('songs');
    }

    public function create(User $user): bool
    {
        return $user->can('songs');
    }

    public function update(User $user, Song $model): bool
    {
        return $user->can('songs');
    }

    public function delete(User $user, Song $model): bool
    {
        return $user->can('songs');
    }
}
