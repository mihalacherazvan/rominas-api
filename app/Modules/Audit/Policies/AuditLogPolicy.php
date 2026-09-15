<?php

declare(strict_types=1);

namespace Rominas\Audit\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Rominas\Audit\Model\AuditLog;
use Rominas\Users\Model\User;

/**
 * The audit trail is gated by the `audit` permission. By default no role is granted it, so only
 * super_admin (via Gate::before) can read the log — audited actors cannot read or scrub their own trail.
 * The trail is append-only: there is no create/update/delete ability.
 */
class AuditLogPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('audit');
    }

    public function view(User $user, AuditLog $model): bool
    {
        return $user->can('audit');
    }
}
