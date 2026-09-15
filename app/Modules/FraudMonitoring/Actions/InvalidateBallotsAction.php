<?php

declare(strict_types=1);

namespace Rominas\FraudMonitoring\Actions;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Rominas\Editions\Enums\EditionStatus;
use Rominas\Editions\Model\Edition;
use Rominas\FraudMonitoring\DataTransferObjects\InvalidateBallotsData;
use Rominas\FraudMonitoring\Model\InvalidationBatch;
use Rominas\Users\Model\User;
use Rominas\Voting\Model\Ballot;

/**
 * Cancel a set of an edition's public ballots as one audited batch. Only the edition's submitted,
 * not-already-invalidated ballots among the requested ids are affected (idempotent on re-run); the batch
 * records the reason and the acting admin, and each cancelled ballot's `invalidation_batch_id` is set so
 * it no longer counts toward Scoring. Invalidation is terminal — there is no reversal.
 */
class InvalidateBallotsAction
{
    /**
     * The edition statuses whose Scoring output is cached (see ComputeEditionScoresAction). Cancelling a
     * ballot changes the public tally, so any cached score for the edition must be busted.
     */
    private const array SCORABLE_STATUSES = [
        EditionStatus::VotingClosed,
        EditionStatus::CommitteeReview,
        EditionStatus::ResultsPublished,
    ];

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function execute(Edition $edition, InvalidateBallotsData $data, User $actor): InvalidationBatch
    {
        $batch = DB::transaction(function () use ($edition, $data, $actor): InvalidationBatch {
            $eligibleIds = Ballot::query()
                ->forEdition($edition)
                ->submitted()
                ->valid()
                ->whereIn('id', $data->ballotIds)
                ->pluck('id');

            if ($eligibleIds->isEmpty()) {
                throw ValidationException::withMessages([
                    'ballot_ids' => 'None of the selected ballots are eligible for invalidation.',
                ]);
            }

            /** @var InvalidationBatch $batch */
            $batch = InvalidationBatch::query()->create([
                'edition_id' => $edition->id,
                'reason' => $data->reason,
                'invalidated_by' => $actor->id,
            ]);

            Ballot::query()
                ->whereIn('id', $eligibleIds->all())
                ->update(['invalidation_batch_id' => $batch->id]);

            return $batch;
        });

        $this->forgetScoringCache($edition);

        return $batch->loadCount('ballots');
    }

    private function forgetScoringCache(Edition $edition): void
    {
        foreach (self::SCORABLE_STATUSES as $status) {
            Cache::forget("scoring:edition:{$edition->id}:{$status->value}");
        }
    }
}
