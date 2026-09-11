<?php

declare(strict_types=1);

namespace Rominas\Taxonomies\Policies;

use Rominas\Taxonomies\Model\Taxonomy;
use Rominas\Users\Model\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaxonomyPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('taxonomies');
    }

    public function view(User $user, Taxonomy $model): bool
    {
        return $user->can('taxonomies');
    }

    public function create(User $user): bool
    {
        return $user->can('taxonomies');
    }

    public function update(User $user, Taxonomy $model): bool
    {
        return $user->can('taxonomies');
    }

    public function delete(User $user, Taxonomy $model): bool
    {
        return $user->can('taxonomies');
    }
}
