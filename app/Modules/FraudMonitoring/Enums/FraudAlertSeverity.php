<?php

declare(strict_types=1);

namespace Rominas\FraudMonitoring\Enums;

/**
 * How far past its detection threshold a finding is, for triage ordering in the admin.
 */
enum FraudAlertSeverity: string
{
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';

    public function label(): string
    {
        return match ($this) {
            self::Low => 'Low',
            self::Medium => 'Medium',
            self::High => 'High',
        };
    }

    /**
     * Derive severity from how far a cluster's size overshoots the detector threshold:
     * >= 3x → high, >= 2x → medium, otherwise low.
     */
    public static function fromCount(int $count, int $threshold): self
    {
        if ($threshold <= 0) {
            return self::Low;
        }

        return match (true) {
            $count >= $threshold * 3 => self::High,
            $count >= $threshold * 2 => self::Medium,
            default => self::Low,
        };
    }
}
