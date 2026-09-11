<?php

declare(strict_types=1);

namespace Rominas\Taxonomies\DataTransferObjects;

class TaxonomyTermData
{
    /**
     * @param  array<string, mixed>|null  $meta
     */
    public function __construct(
        public string $name,
        public int $taxonomy_id,
        public ?int $parentId = null,
        public ?array $meta = null,
    ) {}
}
