<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Rominas\Academy\Nomination\Model\Nomination;
use Rominas\Academy\Nomination\Model\NominationRanking;
use Rominas\Catalog\Artist\Model\Artist;
use Rominas\Catalog\Enums\NomineeType;
use Rominas\Categories\Model\Category;

/**
 * @extends Factory<NominationRanking>
 */
class NominationRankingFactory extends Factory
{
    protected $model = NominationRanking::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nomination_id' => Nomination::factory(),
            'category_id' => Category::factory(),
            'rank' => 1,
            'nominee_type' => NomineeType::Artist,
            'nominee_id' => Artist::factory(),
        ];
    }
}
