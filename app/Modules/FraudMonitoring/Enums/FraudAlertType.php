<?php

declare(strict_types=1);

namespace Rominas\FraudMonitoring\Enums;

/**
 * The kind of suspicious activity a {@see \Rominas\FraudMonitoring\Model\FraudAlert} represents. Each
 * value has a dedicated detector under FraudMonitoring/Detectors/.
 */
enum FraudAlertType: string
{
    case SharedIp = 'shared_ip';
    case VelocityBurst = 'velocity_burst';
    case IdenticalRanking = 'identical_ranking';

    public function label(): string
    {
        return match ($this) {
            self::SharedIp => 'Shared IP cluster',
            self::VelocityBurst => 'Vote velocity burst',
            self::IdenticalRanking => 'Identical ranking pattern',
        };
    }
}
