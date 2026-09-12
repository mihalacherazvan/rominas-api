<?php

declare(strict_types=1);

namespace Rominas\Catalog\Song\Factories;

use Rominas\Catalog\Song\DataTransferObjects\SongData;
use Rominas\Catalog\Song\Requests\CreateSongRequest;
use Rominas\Catalog\Song\Requests\UpdateSongRequest;

class SongDataFactory
{
    public static function fromRequest(CreateSongRequest|UpdateSongRequest $request): SongData
    {
        $validated = $request->validated();

        return new SongData(
            name: $validated['name'],
            description: $validated['description'] ?? null,
        );
    }
}
