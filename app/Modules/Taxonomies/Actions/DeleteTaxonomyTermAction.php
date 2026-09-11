<?php

declare(strict_types=1);

namespace Rominas\Taxonomies\Actions;

use Rominas\Taxonomies\Model\TaxonomyTerm;

class DeleteTaxonomyTermAction
{
    public function execute(TaxonomyTerm $taxonomyTerm): ?bool
    {
        return $taxonomyTerm->delete();
    }
}
