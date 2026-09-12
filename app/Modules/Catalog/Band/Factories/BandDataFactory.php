<?php

declare(strict_types=1);

namespace Rominas\Catalog\Band\Factories;

use Rominas\Catalog\Band\DataTransferObjects\BandData;
use Rominas\Catalog\Band\Requests\CreateBandRequest;
use Rominas\Catalog\Band\Requests\UpdateBandRequest;

class BandDataFactory
{
    public static function fromRequest(CreateBandRequest|UpdateBandRequest $request): BandData
    {
        $validated = $request->validated();

        return new BandData(
            name: $validated['name'],
            description: $validated['description'] ?? null,
        );
    }
}
