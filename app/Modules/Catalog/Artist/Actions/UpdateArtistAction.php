<?php

declare(strict_types=1);

namespace Rominas\Catalog\Artist\Actions;

use Rominas\Catalog\Artist\DataTransferObjects\ArtistData;
use Rominas\Catalog\Artist\Model\Artist;

class UpdateArtistAction
{
    public function execute(Artist $artist, ArtistData $data): Artist
    {
        $artist->name = $data->name;
        $artist->description = $data->description;

        $artist->save();

        return $artist;
    }
}
