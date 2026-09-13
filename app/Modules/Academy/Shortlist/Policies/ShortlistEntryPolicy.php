<?php

declare(strict_types=1);

namespace Rominas\Academy\Shortlist\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Rominas\Academy\Shortlist\Model\ShortlistEntry;
use Rominas\Users\Model\User;

class ShortlistEntryPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('shortlists');
    }

    public function view(User $user, ShortlistEntry $model): bool
    {
        return $user->can('shortlists');
    }

    /**
     * Generating (and regenerating) a shortlist is a create — no instance is bound on the
     * generate routes, so the ability is checked against the class.
     */
    public function create(User $user): bool
    {
        return $user->can('shortlists');
    }
}
