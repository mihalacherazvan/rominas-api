<?php

declare(strict_types=1);

namespace Rominas\Catalog\Album\Actions;

use Rominas\Catalog\Album\DataTransferObjects\AlbumData;
use Rominas\Catalog\Album\Model\Album;

class UpdateAlbumAction
{
    public function execute(Album $album, AlbumData $data): Album
    {
        $album->name = $data->name;
        $album->description = $data->description;

        $album->save();

        return $album;
    }
}
