<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Rominas\Academy\Shortlist\Model\ShortlistEntry;
use Rominas\Catalog\Artist\Model\Artist;
use Rominas\Catalog\Enums\NomineeType;
use Rominas\Categories\Model\Category;
use Rominas\Editions\Model\Edition;

/**
 * @extends Factory<ShortlistEntry>
 */
class ShortlistEntryFactory extends Factory
{
    protected $model = ShortlistEntry::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'edition_id' => Edition::factory(),
            'category_id' => Category::factory(),
            'nominee_type' => NomineeType::Artist,
            'nominee_id' => Artist::factory(),
            'points' => fake()->numberBetween(1, 25),
            'position' => 1,
        ];
    }
}
