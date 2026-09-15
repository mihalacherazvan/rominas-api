<?php

declare(strict_types=1);

namespace Rominas\FraudMonitoring\DataTransferObjects;

use Rominas\FraudMonitoring\Enums\FraudAlertSeverity;
use Rominas\FraudMonitoring\Enums\FraudAlertType;

/**
 * A single suspicious cluster produced by a detector, before it is persisted as a FraudAlert. The
 * `signature` is the stable dedupe key for its type; `context` holds hashes/counts only (no plaintext PII).
 */
final class AlertCandidate
{
    /**
     * @param  array<string, mixed>  $context
     * @param  list<int>  $ballotIds
     */
    public function __construct(
        public readonly FraudAlertType $type,
        public readonly string $signature,
        public readonly FraudAlertSeverity $severity,
        public readonly array $context,
        public readonly array $ballotIds,
    ) {}
}
