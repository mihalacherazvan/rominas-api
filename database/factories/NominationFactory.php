<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Rominas\Academy\Member\Model\Member;
use Rominas\Academy\Nomination\Enums\NominationStatus;
use Rominas\Academy\Nomination\Model\Nomination;
use Rominas\Editions\Model\Edition;

/**
 * @extends Factory<Nomination>
 */
class NominationFactory extends Factory
{
    protected $model = Nomination::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'member_id' => Member::factory(),
            'edition_id' => Edition::factory(),
            'status' => NominationStatus::Draft,
            'submitted_at' => null,
        ];
    }

    public function submitted(): static
    {
        return $this->state(fn(array $attributes): array => [
            'status' => NominationStatus::Submitted,
            'submitted_at' => now(),
        ]);
    }
}
