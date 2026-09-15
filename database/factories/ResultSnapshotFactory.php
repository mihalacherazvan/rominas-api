<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Rominas\Editions\Model\Edition;
use Rominas\Results\Model\ResultSnapshot;

/**
 * @extends Factory<ResultSnapshot>
 */
class ResultSnapshotFactory extends Factory
{
    protected $model = ResultSnapshot::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'edition_id' => Edition::factory(),
            'academy_vote_weight' => 60,
            'public_vote_weight' => 40,
            'published_at' => now(),
        ];
    }
}
