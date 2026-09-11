<?php

declare(strict_types=1);

namespace Database\Factories;

use Rominas\Taxonomies\Model\Taxonomy;
use Rominas\Taxonomies\Model\TaxonomyTerm;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<TaxonomyTerm>
 */
class TaxonomyTermFactory extends Factory
{
    protected $model = TaxonomyTerm::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'meta' => null,
            'taxonomy_id' => Taxonomy::factory(),
            'parent_id' => null,
        ];
    }
}
