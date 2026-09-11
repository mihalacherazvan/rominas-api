<?php

declare(strict_types=1);

namespace Rominas\Taxonomies\Factories;

use Rominas\Taxonomies\DataTransferObjects\TaxonomyData;
use Rominas\Taxonomies\Requests\CreateTaxonomyRequest;
use Rominas\Taxonomies\Requests\UpdateTaxonomyRequest;

class TaxonomyDataFactory
{
    public static function fromRequest(CreateTaxonomyRequest|UpdateTaxonomyRequest $request): TaxonomyData
    {
        return new TaxonomyData(...$request->validated());
    }
}
