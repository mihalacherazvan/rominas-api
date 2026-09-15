<?php

declare(strict_types=1);

namespace Rominas\FraudMonitoring\DataTransferObjects;

/**
 * A vote-cancellation request: the ballots to invalidate and the mandatory reason for the whole batch.
 * The edition and the acting admin are supplied to the action from the route/auth context.
 */
final class InvalidateBallotsData
{
    /**
     * @param  list<int>  $ballotIds
     */
    public function __construct(
        public readonly array $ballotIds,
        public readonly string $reason,
    ) {}
}
