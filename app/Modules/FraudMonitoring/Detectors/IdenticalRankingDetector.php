<?php

declare(strict_types=1);

namespace Rominas\FraudMonitoring\Detectors;

use Illuminate\Support\Collection;
use Rominas\Editions\Model\Edition;
use Rominas\FraudMonitoring\DataTransferObjects\AlertCandidate;
use Rominas\FraudMonitoring\Enums\FraudAlertSeverity;
use Rominas\FraudMonitoring\Enums\FraudAlertType;
use Rominas\Voting\Model\BallotRanking;
use Rominas\Voting\QueryBuilders\BallotQueryBuilder;

/**
 * Flags groups of ballots that cast a byte-identical ordered vote set across all categories — a
 * scripted/bot fingerprint, since independent voters practically never produce the same full permutation.
 * A per-ballot signature is the sha256 of its rankings ordered by (category, rank, nominee); ballots with
 * no rankings are ignored.
 */
class IdenticalRankingDetector implements FraudDetector
{
    public function detect(Edition $edition): array
    {
        $threshold = (int) config('fraud.identical_ranking.threshold');

        /** @var Collection<int, BallotRanking> $rankings */
        $rankings = BallotRanking::query()
            ->whereHas(
                'ballot',
                fn(BallotQueryBuilder $query) => $query->forEdition($edition)->submitted()->valid(),
            )
            ->orderBy('ballot_id')
            ->orderBy('category_id')
            ->orderBy('rank')
            ->get(['ballot_id', 'category_id', 'rank', 'nominee_type', 'nominee_id']);

        /** @var array<string, list<int>> $ballotsBySignature */
        $ballotsBySignature = [];

        foreach ($rankings->groupBy('ballot_id') as $ballotId => $rows) {
            $parts = $rows->map(
                fn(BallotRanking $row): string => $row->category_id . ':' . $row->rank . ':'
                    . $row->nominee_type->value . ':' . $row->nominee_id,
            )->all();

            $signature = hash('sha256', implode('|', $parts));
            $ballotsBySignature[$signature][] = (int) $ballotId;
        }

        $candidates = [];

        foreach ($ballotsBySignature as $signature => $ballotIds) {
            $count = count($ballotIds);

            if ($count < $threshold) {
                continue;
            }

            $candidates[] = new AlertCandidate(
                type: FraudAlertType::IdenticalRanking,
                signature: $signature,
                severity: FraudAlertSeverity::fromCount($count, $threshold),
                context: ['ranking_hash' => $signature, 'count' => $count],
                ballotIds: $ballotIds,
            );
        }

        return $candidates;
    }
}
