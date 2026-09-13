<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Rominas\Editions\Model\Edition;
use Rominas\Voting\Enums\BallotStatus;
use Rominas\Voting\Model\Ballot;

/**
 * @extends Factory<Ballot>
 */
class BallotFactory extends Factory
{
    protected $model = Ballot::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'edition_id' => Edition::factory(),
            'email_hash' => hash('sha256', fake()->unique()->safeEmail()),
            'token_hash' => hash('sha256', Str::random(48)),
            'status' => BallotStatus::Issued,
            'expires_at' => now()->addWeek(),
            'submitted_at' => null,
            'ip_hash' => null,
        ];
    }

    public function submitted(): static
    {
        return $this->state(fn(array $attributes): array => [
            'status' => BallotStatus::Submitted,
            'submitted_at' => now(),
        ]);
    }
}
