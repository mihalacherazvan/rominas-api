<?php

declare(strict_types=1);

namespace Rominas\Catalog\Album\Actions;

use Rominas\Catalog\Album\DataTransferObjects\AlbumData;
use Rominas\Catalog\Album\Model\Album;

class CreateAlbumAction
{
    public function execute(AlbumData $data): Album
    {
        return Album::create([
            'name' => $data->name,
            'description' => $data->description,
        ]);
    }
}
