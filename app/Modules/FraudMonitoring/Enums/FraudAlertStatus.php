<?php

declare(strict_types=1);

namespace Rominas\FraudMonitoring\Enums;

/**
 * The review state of a fraud alert. Alerts are created `pending`; a fraud monitor / custodian moves them
 * to `solved` (acted on — typically the implicated ballots were invalidated) or `dismissed` (false
 * positive). Detection never overwrites a human-set status.
 */
enum FraudAlertStatus: string
{
    case Pending = 'pending';
    case Solved = 'solved';
    case Dismissed = 'dismissed';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Solved => 'Solved',
            self::Dismissed => 'Dismissed',
        };
    }
}
