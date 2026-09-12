<?php

declare(strict_types=1);

namespace Rominas\Catalog\Venue\Factories;

use Rominas\Catalog\Venue\DataTransferObjects\VenueData;
use Rominas\Catalog\Venue\Requests\CreateVenueRequest;
use Rominas\Catalog\Venue\Requests\UpdateVenueRequest;

class VenueDataFactory
{
    public static function fromRequest(CreateVenueRequest|UpdateVenueRequest $request): VenueData
    {
        $validated = $request->validated();

        return new VenueData(
            name: $validated['name'],
            description: $validated['description'] ?? null,
        );
    }
}
