<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Rominas\Catalog\Artist\Model\Artist;
use Rominas\Catalog\Enums\NomineeType;
use Rominas\Categories\Model\Category;
use Rominas\Results\Model\ResultEntry;
use Rominas\Results\Model\ResultSnapshot;

/**
 * @extends Factory<ResultEntry>
 */
class ResultEntryFactory extends Factory
{
    protected $model = ResultEntry::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'result_snapshot_id' => ResultSnapshot::factory(),
            'category_id' => Category::factory(),
            'nominee_type' => NomineeType::Artist,
            'nominee_id' => Artist::factory(),
            'academy_points' => fake()->numberBetween(0, 25),
            'public_points' => fake()->numberBetween(0, 25),
            'academy_share' => fake()->randomFloat(6, 0, 1),
            'public_share' => fake()->randomFloat(6, 0, 1),
            'final_score' => fake()->randomFloat(6, 0, 1),
            'position' => 1,
        ];
    }
}
