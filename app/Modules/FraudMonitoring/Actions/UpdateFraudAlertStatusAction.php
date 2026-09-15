<?php

declare(strict_types=1);

namespace Rominas\FraudMonitoring\Actions;

use Rominas\FraudMonitoring\Enums\FraudAlertStatus;
use Rominas\FraudMonitoring\Model\FraudAlert;

/**
 * Sets a fraud alert's review status. Marking `solved` does NOT itself invalidate ballots — cancelling
 * votes stays the separate, explicit InvalidateBallotsAction the monitor runs first.
 */
class UpdateFraudAlertStatusAction
{
    public function execute(FraudAlert $alert, FraudAlertStatus $status): FraudAlert
    {
        $alert->status = $status;
        $alert->save();

        return $alert;
    }
}
