<?php

declare(strict_types=1);

namespace Rominas\Catalog\Artist\Factories;

use Rominas\Catalog\Artist\DataTransferObjects\ArtistData;
use Rominas\Catalog\Artist\Requests\CreateArtistRequest;
use Rominas\Catalog\Artist\Requests\UpdateArtistRequest;

class ArtistDataFactory
{
    public static function fromRequest(CreateArtistRequest|UpdateArtistRequest $request): ArtistData
    {
        $validated = $request->validated();

        return new ArtistData(
            name: $validated['name'],
            description: $validated['description'] ?? null,
        );
    }
}
