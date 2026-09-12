<?php

declare(strict_types=1);

namespace Rominas\Catalog\Album\Factories;

use Rominas\Catalog\Album\DataTransferObjects\AlbumData;
use Rominas\Catalog\Album\Requests\CreateAlbumRequest;
use Rominas\Catalog\Album\Requests\UpdateAlbumRequest;

class AlbumDataFactory
{
    public static function fromRequest(CreateAlbumRequest|UpdateAlbumRequest $request): AlbumData
    {
        $validated = $request->validated();

        return new AlbumData(
            name: $validated['name'],
            description: $validated['description'] ?? null,
        );
    }
}
