<?php

declare(strict_types=1);

namespace Rominas\Scoring\DataTransferObjects;

/**
 * A category's final result: its shortlisted nominees ranked best-first. Empty `nominees` means the
 * category has no shortlist to score.
 */
final class CategoryScore
{
    /**
     * @param  list<NomineeScore>  $nominees  ordered by position (1 = winner)
     */
    public function __construct(
        public readonly int $categoryId,
        public readonly array $nominees,
    ) {}
}
