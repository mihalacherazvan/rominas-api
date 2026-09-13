<?php

declare(strict_types=1);

namespace Rominas\Scoring;

use InvalidArgumentException;

/**
 * The confirmed academy points-per-rank curve (client, 2026-09-13): a nominee ranked `r` in a ballot
 * earns `6 − r` points, i.e. rank 1 → 5 pts … rank 5 → 1 pt. This is the single source of truth for the
 * curve; both the nominee shortlist aggregation and (later) the Voting/Scoring weighting read it here
 * rather than inlining the formula. The 60/40 academy-vs-public weighting lands with the Scoring module.
 */
final class RankPoints
{
    /** The number of ranked slots per category (top-N). */
    public const int RANKS = 5;

    /** Points for rank `r` are `BASE − r`, so rank 1 tops out at `BASE − 1`. */
    private const int BASE = self::RANKS + 1;

    public static function forRank(int $rank): int
    {
        if ($rank < 1 || $rank > self::RANKS) {
            throw new InvalidArgumentException("Rank must be between 1 and " . self::RANKS . ", got {$rank}.");
        }

        return self::BASE - $rank;
    }
}
