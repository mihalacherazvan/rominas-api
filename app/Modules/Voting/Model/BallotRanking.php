<?php

declare(strict_types=1);

namespace Rominas\Voting\Model;

use Database\Factories\BallotRankingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Rominas\Catalog\Enums\NomineeType;
use Rominas\Categories\Model\Category;

/**
 * One ranked pick on a public ballot: the voter placed a shortlisted nominee (`nominee`) at position
 * `rank` (1 = favourite) in a category. Mirrors NominationRanking. Points are derived later by Scoring
 * (rank → 6−rank); this row stores the rank only. Nominee is polymorphic via the app-wide morph map.
 *
 * @mixin IdeHelperBallotRanking
 */
#[Fillable([
    'ballot_id',
    'category_id',
    'rank',
    'nominee_type',
    'nominee_id',
])]
class BallotRanking extends Model
{
    /** @use HasFactory<BallotRankingFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'rank' => 'integer',
            'nominee_type' => NomineeType::class,
        ];
    }

    /**
     * @return BelongsTo<Ballot, $this>
     */
    public function ballot(): BelongsTo
    {
        return $this->belongsTo(Ballot::class);
    }

    /**
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function nominee(): MorphTo
    {
        return $this->morphTo();
    }
}
