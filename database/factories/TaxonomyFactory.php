<?php

declare(strict_types=1);

namespace Database\Factories;

use Rominas\Taxonomies\Model\Taxonomy;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Taxonomy>
 */
class TaxonomyFactory extends Factory
{
    protected $model = Taxonomy::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'hierarchical' => false,
            'allows_multiple' => false,
        ];
    }

    public function hierarchical(): static
    {
        return $this->state(fn(array $attributes): array => ['hierarchical' => true]);
    }

    public function allowsMultiple(): static
    {
        return $this->state(fn(array $attributes): array => ['allows_multiple' => true]);
    }
}
