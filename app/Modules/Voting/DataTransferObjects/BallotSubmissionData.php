<?php

declare(strict_types=1);

namespace Rominas\Voting\DataTransferObjects;

/**
 * A public voter's whole ballot submission: the link token, the per-category votes, and the raw request
 * IP (hashed at persist time for FraudMonitoring — never stored in plaintext).
 */
final class BallotSubmissionData
{
    /**
     * @param  list<CategoryVoteData>  $categories
     */
    public function __construct(
        public readonly string $token,
        public readonly array $categories,
        public readonly ?string $ip,
    ) {}
}
