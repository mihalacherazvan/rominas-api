<?php

declare(strict_types=1);

namespace Rominas\FraudMonitoring\Detectors;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Rominas\Editions\Model\Edition;
use Rominas\FraudMonitoring\DataTransferObjects\AlertCandidate;
use Rominas\FraudMonitoring\Enums\FraudAlertSeverity;
use Rominas\FraudMonitoring\Enums\FraudAlertType;
use Rominas\Voting\Model\Ballot;

/**
 * Flags fixed-size time windows (`fraud.velocity.window_minutes`) in which at least
 * `fraud.velocity.threshold` submitted, still-valid ballots were cast — an automated-flood signal. Windows
 * are aligned to the epoch so the window a ballot falls in (and thus a burst's signature) is stable across
 * runs.
 */
class VelocityBurstDetector implements FraudDetector
{
    public function detect(Edition $edition): array
    {
        $windowSeconds = max(1, (int) config('fraud.velocity.window_minutes') * 60);
        $threshold = (int) config('fraud.velocity.threshold');

        /** @var Collection<int, Ballot> $ballots */
        $ballots = Ballot::query()
            ->forEdition($edition)
            ->submitted()
            ->valid()
            ->whereNotNull('submitted_at')
            ->get(['id', 'submitted_at']);

        /** @var Collection<int, Collection<int, Ballot>> $buckets */
        $buckets = $ballots->groupBy(
            fn(Ballot $ballot): int => intdiv((int) $ballot->submitted_at->timestamp, $windowSeconds),
        );

        $candidates = [];

        foreach ($buckets as $bucketIndex => $group) {
            $count = $group->count();

            if ($count < $threshold) {
                continue;
            }

            $windowStart = Carbon::createFromTimestampUTC($bucketIndex * $windowSeconds);
            $windowEnd = $windowStart->copy()->addSeconds($windowSeconds);

            $candidates[] = new AlertCandidate(
                type: FraudAlertType::VelocityBurst,
                signature: $windowStart->toIso8601String(),
                severity: FraudAlertSeverity::fromCount($count, $threshold),
                context: [
                    'window_start' => $windowStart->toIso8601String(),
                    'window_end' => $windowEnd->toIso8601String(),
                    'count' => $count,
                ],
                ballotIds: $group->pluck('id')->map(fn($id): int => (int) $id)->all(),
            );
        }

        return $candidates;
    }
}
