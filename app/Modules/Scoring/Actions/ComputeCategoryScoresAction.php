<?php

declare(strict_types=1);

namespace Rominas\Scoring\Actions;

use Illuminate\Validation\ValidationException;
use Rominas\Academy\Nomination\Model\NominationRanking;
use Rominas\Academy\Nomination\QueryBuilders\NominationQueryBuilder;
use Rominas\Academy\Shortlist\Model\ShortlistEntry;
use Rominas\Catalog\Enums\NomineeType;
use Rominas\Categories\Model\Category;
use Rominas\Editions\Enums\EditionStatus;
use Rominas\Editions\Model\Edition;
use Rominas\Scoring\DataTransferObjects\CategoryScore;
use Rominas\Scoring\RankPoints;
use Rominas\Scoring\Support\ScoreCalculator;
use Rominas\Voting\Model\BallotRanking;
use Rominas\Voting\QueryBuilders\BallotQueryBuilder;

/**
 * Computes one category's final result. The contested nominee set is the category's shortlist; each
 * nominee's academy and public points are re-tallied from the raw submitted rankings (the source of
 * truth, robust to any manual shortlist adjustment) with the {@see RankPoints} curve, then handed to
 * {@see ScoreCalculator} for the 60/40 weighting and ranking.
 *
 * Guard: only once public voting has closed (`voting_closed` onward) — before that the public side is
 * not final. Nothing is persisted; the result is computed on demand.
 */
class ComputeCategoryScoresAction
{
    private const array SCORABLE_STATUSES = [
        EditionStatus::VotingClosed,
        EditionStatus::CommitteeReview,
        EditionStatus::ResultsPublished,
    ];

    public function __construct(
        private readonly ScoreCalculator $calculator,
    ) {}

    public function execute(Edition $edition, Category $category): CategoryScore
    {
        $this->assertScorable($edition);

        if ($category->edition_id !== $edition->id) {
            throw ValidationException::withMessages([
                'category' => 'The category does not belong to this edition.',
            ]);
        }

        return new CategoryScore(
            categoryId: $category->id,
            nominees: $this->calculator->rank(
                $this->tally($edition, $category),
                $edition->academy_vote_weight,
                $edition->public_vote_weight,
            ),
        );
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function assertScorable(Edition $edition): void
    {
        if (! in_array($edition->status, self::SCORABLE_STATUSES, true)) {
            throw ValidationException::withMessages([
                'status' => 'Scores can only be computed once public voting has closed.',
            ]);
        }
    }

    /**
     * Build the per-nominee tally for the category's shortlisted nominees, summing academy points from
     * submitted nominations and public points from submitted ballots. Nominees with no points on a side
     * default to 0 there.
     *
     * @return list<array{nominee_type: NomineeType, nominee_id: int, academy_points: int, public_points: int}>
     */
    private function tally(Edition $edition, Category $category): array
    {
        $shortlist = ShortlistEntry::query()
            ->forEdition($edition)
            ->forCategory($category)
            ->get();

        if ($shortlist->isEmpty()) {
            return [];
        }

        $academy = $this->sumAcademyPoints($edition, $category);
        $public = $this->sumPublicPoints($edition, $category);

        $tallies = [];

        foreach ($shortlist as $entry) {
            $key = $entry->nominee_type->value . ':' . $entry->nominee_id;

            $tallies[] = [
                'nominee_type' => $entry->nominee_type,
                'nominee_id' => (int) $entry->nominee_id,
                'academy_points' => $academy[$key] ?? 0,
                'public_points' => $public[$key] ?? 0,
            ];
        }

        return $tallies;
    }

    /**
     * @return array<string, int>  keyed by "<nominee_type>:<nominee_id>"
     */
    private function sumAcademyPoints(Edition $edition, Category $category): array
    {
        $picks = NominationRanking::query()
            ->whereHas('nomination', fn(NominationQueryBuilder $query) => $query->forEdition($edition)->submitted())
            ->where('category_id', '=', $category->id)
            ->get(['nominee_type', 'nominee_id', 'rank'])
            ->map(fn(NominationRanking $ranking): array => [
                'type' => $ranking->nominee_type,
                'id' => (int) $ranking->nominee_id,
                'rank' => $ranking->rank,
            ])
            ->all();

        return $this->sumByNominee($picks);
    }

    /**
     * @return array<string, int>  keyed by "<nominee_type>:<nominee_id>"
     */
    private function sumPublicPoints(Edition $edition, Category $category): array
    {
        $picks = BallotRanking::query()
            ->whereHas('ballot', fn(BallotQueryBuilder $query) => $query->forEdition($edition)->submitted()->valid())
            ->where('category_id', '=', $category->id)
            ->get(['nominee_type', 'nominee_id', 'rank'])
            ->map(fn(BallotRanking $ranking): array => [
                'type' => $ranking->nominee_type,
                'id' => (int) $ranking->nominee_id,
                'rank' => $ranking->rank,
            ])
            ->all();

        return $this->sumByNominee($picks);
    }

    /**
     * @param  list<array{type: NomineeType, id: int, rank: int}>  $picks
     * @return array<string, int>
     */
    private function sumByNominee(array $picks): array
    {
        $totals = [];

        foreach ($picks as $pick) {
            $key = $pick['type']->value . ':' . $pick['id'];
            $totals[$key] = ($totals[$key] ?? 0) + RankPoints::forRank($pick['rank']);
        }

        return $totals;
    }
}
