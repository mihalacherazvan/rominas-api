<?php

declare(strict_types=1);

namespace Rominas\Taxonomies\DataTransferObjects;

class TaxonomyData
{
    public function __construct(
        public string $name,
        public bool $hierarchical = false,
        public bool $allows_multiple = false,
    ) {}
}
