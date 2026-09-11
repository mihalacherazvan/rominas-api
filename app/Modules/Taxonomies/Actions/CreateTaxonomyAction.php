<?php

declare(strict_types=1);

namespace Rominas\Taxonomies\Actions;

use Rominas\Taxonomies\DataTransferObjects\TaxonomyData;
use Rominas\Taxonomies\Model\Taxonomy;

class CreateTaxonomyAction
{
    public function execute(TaxonomyData $taxonomyData): Taxonomy
    {
        return Taxonomy::create([
            'name' => $taxonomyData->name,
            'hierarchical' => $taxonomyData->hierarchical,
            'allows_multiple' => $taxonomyData->allows_multiple,
        ]);
    }
}
