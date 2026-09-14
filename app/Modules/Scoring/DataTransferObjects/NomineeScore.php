<?php

declare(strict_types=1);

namespace Rominas\Scoring\DataTransferObjects;

use Rominas\Catalog\Enums\NomineeType;

/**
 * One nominee's computed standing within a category: the raw summed points on each side, each side's
 * normalized share (0..1) of its class total, the weighted `finalScore` (0..1), and the resulting
 * `position` (1 = winner). Shares and score are rounded for display only — the ordering was decided
 * on an exact integer key in {@see \Rominas\Scoring\Support\ScoreCalculator}.
 */
final class NomineeScore
{
    public function __construct(
        public readonly NomineeType $nomineeType,
        public readonly int $nomineeId,
        public readonly int $academyPoints,
        public readonly int $publicPoints,
        public readonly float $academyShare,
        public readonly float $publicShare,
        public readonly float $finalScore,
        public readonly int $position,
    ) {}
}
