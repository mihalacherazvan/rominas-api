<?php

declare(strict_types=1);

namespace Rominas\Academy\MemberProposal\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Rominas\Academy\MemberProposal\Model\MemberProposal;
use Rominas\Users\Model\User;

/**
 * Admin-side review of member proposals. Gates on the `memberProposals` permission. The member-facing
 * side (create/list/withdraw own proposals) is guarded by `auth:member` with implicit ownership, not
 * this policy.
 */
class MemberProposalPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('memberProposals');
    }

    public function view(User $user, MemberProposal $model): bool
    {
        return $user->can('memberProposals');
    }

    public function update(User $user, MemberProposal $model): bool
    {
        return $user->can('memberProposals');
    }
}
