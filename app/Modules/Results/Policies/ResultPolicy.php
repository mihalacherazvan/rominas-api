<?php

declare(strict_types=1);

namespace Rominas\Results\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Rominas\Results\Model\ResultSnapshot;
use Rominas\Users\Model\User;

/**
 * Final results are custodian-gated: only holders of the `results` permission (the custodian role, plus
 * super_admin via Gate::before) may view or export an edition's complete results before publication.
 * The ability is checked against the class — the view/export routes bind an Edition, not a snapshot row.
 */
class ResultPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('results');
    }

    public function view(User $user, ResultSnapshot $model): bool
    {
        return $user->can('results');
    }
}
