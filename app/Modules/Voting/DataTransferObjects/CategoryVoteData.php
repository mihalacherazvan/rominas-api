<?php

declare(strict_types=1);

namespace Rominas\Voting\DataTransferObjects;

/**
 * One category's vote: the shortlisted nominee ids in rank order (index 0 = rank 1 = favourite).
 */
final class CategoryVoteData
{
    /**
     * @param  list<int>  $nomineeIds
     */
    public function __construct(
        public readonly int $categoryId,
        public readonly array $nomineeIds,
    ) {}
}
