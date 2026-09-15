<?php

declare(strict_types=1);

namespace Rominas\FraudMonitoring\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Rominas\FraudMonitoring\Model\InvalidationBatch;
use Rominas\Users\Model\User;

/**
 * Fraud monitoring is gated by the `fraudMonitoring` permission (the `fraud_monitor` and `custodian`
 * roles, plus super_admin via Gate::before). The ability is checked against the class — the routes bind
 * an Edition, not a batch row. Every fraud endpoint, including the ballot listing, authorizes here so no
 * separate Ballot policy is needed.
 */
class InvalidationBatchPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('fraudMonitoring');
    }

    public function view(User $user, InvalidationBatch $model): bool
    {
        return $user->can('fraudMonitoring');
    }

    public function create(User $user): bool
    {
        return $user->can('fraudMonitoring');
    }
}
