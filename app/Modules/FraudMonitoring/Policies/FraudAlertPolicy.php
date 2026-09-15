<?php

declare(strict_types=1);

namespace Rominas\FraudMonitoring\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Rominas\FraudMonitoring\Model\FraudAlert;
use Rominas\Users\Model\User;

/**
 * Fraud alerts are gated by the same `fraudMonitoring` permission as vote invalidation (the
 * `fraud_monitor` and `custodian` roles, plus super_admin via Gate::before). Viewing is class-level (the
 * routes bind an Edition); updating the status binds the alert instance.
 */
class FraudAlertPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('fraudMonitoring');
    }

    public function view(User $user, FraudAlert $model): bool
    {
        return $user->can('fraudMonitoring');
    }

    public function update(User $user, FraudAlert $model): bool
    {
        return $user->can('fraudMonitoring');
    }
}
