<?php

declare(strict_types=1);

namespace Rominas\Academy\Member\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Rominas\Academy\Member\Model\Member;
use Rominas\Users\Model\User;

/**
 * Admin-side authorization for managing the academy roster. Gates on the plain `members` permission
 * (granted to the `admin` role; `super_admin` bypasses via Gate::before). This is separate from the
 * member-facing magic-link auth, which is guarded by `auth:member`.
 */
class MemberPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('members');
    }

    public function view(User $user, Member $model): bool
    {
        return $user->can('members');
    }

    public function create(User $user): bool
    {
        return $user->can('members');
    }

    public function update(User $user, Member $model): bool
    {
        return $user->can('members');
    }

    public function delete(User $user, Member $model): bool
    {
        return $user->can('members');
    }
}
