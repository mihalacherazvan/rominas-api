<?php

declare(strict_types=1);

namespace Rominas\Taxonomies\Actions;

use Rominas\Taxonomies\DataTransferObjects\TaxonomyData;
use Rominas\Taxonomies\Model\Taxonomy;

class UpdateTaxonomyAction
{
    public function execute(Taxonomy $taxonomy, TaxonomyData $taxonomyData): Taxonomy
    {
        $taxonomy->name = $taxonomyData->name;
        $taxonomy->hierarchical = $taxonomyData->hierarchical;
        $taxonomy->allows_multiple = $taxonomyData->allows_multiple;

        $taxonomy->save();

        return $taxonomy;
    }
}
