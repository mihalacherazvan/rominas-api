<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Rominas\Catalog\Artist\Model\Artist;
use Rominas\Catalog\Enums\NomineeType;
use Rominas\Categories\Model\Category;
use Rominas\Voting\Model\Ballot;
use Rominas\Voting\Model\BallotRanking;

/**
 * @extends Factory<BallotRanking>
 */
class BallotRankingFactory extends Factory
{
    protected $model = BallotRanking::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ballot_id' => Ballot::factory(),
            'category_id' => Category::factory(),
            'rank' => 1,
            'nominee_type' => NomineeType::Artist,
            'nominee_id' => Artist::factory(),
        ];
    }
}
