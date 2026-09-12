<?php

declare(strict_types=1);

namespace Rominas\Catalog\Artist\Actions;

use Rominas\Catalog\Artist\DataTransferObjects\ArtistData;
use Rominas\Catalog\Artist\Model\Artist;

class CreateArtistAction
{
    public function execute(ArtistData $data): Artist
    {
        return Artist::create([
            'name' => $data->name,
            'description' => $data->description,
        ]);
    }
}
