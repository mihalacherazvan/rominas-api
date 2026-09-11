<?php

declare(strict_types=1);

namespace Rominas\Taxonomies\Actions;

use Rominas\Taxonomies\Model\Taxonomy;

class DeleteTaxonomyAction
{
    public function execute(Taxonomy $taxonomy): ?bool
    {
        return $taxonomy->delete();
    }
}
