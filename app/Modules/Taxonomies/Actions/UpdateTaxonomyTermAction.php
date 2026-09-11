<?php

declare(strict_types=1);

namespace Rominas\Taxonomies\Actions;

use Rominas\Taxonomies\DataTransferObjects\TaxonomyTermData;
use Rominas\Taxonomies\Model\Taxonomy;
use Rominas\Taxonomies\Model\TaxonomyTerm;

class UpdateTaxonomyTermAction
{
    public function execute(TaxonomyTerm $taxonomyTerm, TaxonomyTermData $taxonomyTermData): TaxonomyTerm
    {
        $taxonomyTerm->name = $taxonomyTermData->name;
        // `meta` is cast through TaxonomyTermMetaCast; setAttribute keeps the array shape
        // the cast expects without tripping the raw column's string type.
        $taxonomyTerm->setAttribute('meta', $taxonomyTermData->meta);

        $taxonomyTerm->taxonomy()->associate(
            Taxonomy::query()->findOrFail($taxonomyTermData->taxonomy_id),
        );

        if ($taxonomyTermData->parentId !== null) {
            $taxonomyTerm->parent()->associate(
                TaxonomyTerm::query()->findOrFail($taxonomyTermData->parentId),
            );
        } else {
            $taxonomyTerm->parent()->dissociate();
        }

        $taxonomyTerm->save();

        return $taxonomyTerm;
    }
}
