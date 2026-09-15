<?php

declare(strict_types=1);

namespace Rominas\FraudMonitoring\Detectors;

use Rominas\Editions\Model\Edition;
use Rominas\FraudMonitoring\DataTransferObjects\AlertCandidate;

/**
 * One suspicious-activity heuristic over an edition's submitted, still-valid public ballots. Returns the
 * clusters that meet its threshold; persistence and dedupe are handled by DetectVotingFraudAction.
 */
interface FraudDetector
{
    /**
     * @return list<AlertCandidate>
     */
    public function detect(Edition $edition): array;
}
