<?php

declare(strict_types=1);

namespace Rominas\Taxonomies\Policies;

use Rominas\Taxonomies\Model\TaxonomyTerm;
use Rominas\Users\Model\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaxonomyTermPolicy
{
    use HandlesAuthorization;

    /**
     * Any user may list terms; the query builder filters them down to what the
     * user's permissions allow.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, TaxonomyTerm $model): bool
    {
        return $user->can('taxonomyTerms');
    }

    public function create(User $user): bool
    {
        return $user->can('taxonomyTerms');
    }

    public function update(User $user, TaxonomyTerm $model): bool
    {
        return $user->can('taxonomyTerms');
    }

    public function delete(User $user, TaxonomyTerm $model): bool
    {
        return $user->can('taxonomyTerms');
    }
}
