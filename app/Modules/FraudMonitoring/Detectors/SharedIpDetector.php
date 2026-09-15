<?php

declare(strict_types=1);

namespace Rominas\FraudMonitoring\Detectors;

use Illuminate\Support\Collection;
use Rominas\Editions\Model\Edition;
use Rominas\FraudMonitoring\DataTransferObjects\AlertCandidate;
use Rominas\FraudMonitoring\Enums\FraudAlertSeverity;
use Rominas\FraudMonitoring\Enums\FraudAlertType;
use Rominas\Voting\Model\Ballot;

/**
 * Flags each ip_hash shared by at least `fraud.shared_ip.threshold` submitted, still-valid ballots — the
 * primary cross-ballot signal, since one link per email means repeat voting shows up as many emails from
 * one IP. ip_hash is nullable, so only ballots with a captured IP are considered.
 */
class SharedIpDetector implements FraudDetector
{
    public function detect(Edition $edition): array
    {
        $threshold = (int) config('fraud.shared_ip.threshold');

        /** @var Collection<string, Collection<int, Ballot>> $groups */
        $groups = Ballot::query()
            ->forEdition($edition)
            ->submitted()
            ->valid()
            ->whereNotNull('ip_hash')
            ->get(['id', 'ip_hash'])
            ->groupBy('ip_hash');

        $candidates = [];

        foreach ($groups as $ipHash => $ballots) {
            $count = $ballots->count();

            if ($count < $threshold) {
                continue;
            }

            $candidates[] = new AlertCandidate(
                type: FraudAlertType::SharedIp,
                signature: (string) $ipHash,
                severity: FraudAlertSeverity::fromCount($count, $threshold),
                context: ['ip_hash' => (string) $ipHash, 'count' => $count],
                ballotIds: $ballots->pluck('id')->map(fn($id): int => (int) $id)->all(),
            );
        }

        return $candidates;
    }
}
