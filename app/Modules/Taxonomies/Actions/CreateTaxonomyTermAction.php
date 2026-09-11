<?php

declare(strict_types=1);

namespace Rominas\Taxonomies\Actions;

use Rominas\Taxonomies\DataTransferObjects\TaxonomyTermData;
use Rominas\Taxonomies\Model\Taxonomy;
use Rominas\Taxonomies\Model\TaxonomyTerm;

class CreateTaxonomyTermAction
{
    public function execute(TaxonomyTermData $taxonomyTermData): TaxonomyTerm
    {
        $taxonomyTerm = new TaxonomyTerm([
            'name' => $taxonomyTermData->name,
            'meta' => $taxonomyTermData->meta,
        ]);

        $taxonomyTerm->taxonomy()->associate(
            Taxonomy::query()->findOrFail($taxonomyTermData->taxonomy_id),
        );

        if ($taxonomyTermData->parentId !== null) {
            $taxonomyTerm->parent()->associate(
                TaxonomyTerm::query()->findOrFail($taxonomyTermData->parentId),
            );
        }

        $taxonomyTerm->save();

        return $taxonomyTerm;
    }
}
