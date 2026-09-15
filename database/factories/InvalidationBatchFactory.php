<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Rominas\Editions\Model\Edition;
use Rominas\FraudMonitoring\Model\InvalidationBatch;
use Rominas\Users\Model\User;

/**
 * @extends Factory<InvalidationBatch>
 */
class InvalidationBatchFactory extends Factory
{
    protected $model = InvalidationBatch::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'edition_id' => Edition::factory(),
            'reason' => fake()->sentence(),
            'invalidated_by' => User::factory(),
        ];
    }
}
