<?php

declare(strict_types=1);

namespace Rominas\Taxonomies\Factories;

use Rominas\Taxonomies\DataTransferObjects\TaxonomyTermData;
use Rominas\Taxonomies\Requests\CreateTaxonomyTermRequest;
use Rominas\Taxonomies\Requests\UpdateTaxonomyTermRequest;

class TaxonomyTermDataFactory
{
    public static function fromRequest(CreateTaxonomyTermRequest|UpdateTaxonomyTermRequest $request): TaxonomyTermData
    {
        return new TaxonomyTermData(...$request->validated());
    }
}
