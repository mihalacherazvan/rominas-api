<?php

declare(strict_types=1);

namespace Rominas\Catalog\Venue\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Rominas\Catalog\Venue\Model\Venue;
use Rominas\Users\Model\User;

class VenuePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('venues');
    }

    public function view(User $user, Venue $model): bool
    {
        return $user->can('venues');
    }

    public function create(User $user): bool
    {
        return $user->can('venues');
    }

    public function update(User $user, Venue $model): bool
    {
        return $user->can('venues');
    }

    public function delete(User $user, Venue $model): bool
    {
        return $user->can('venues');
    }
}
