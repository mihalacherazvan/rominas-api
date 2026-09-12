<?php

declare(strict_types=1);

namespace Rominas\Catalog\Album\Actions;

use Rominas\Catalog\Album\Model\Album;

class DeleteAlbumAction
{
    public function execute(Album $album): ?bool
    {
        return $album->delete();
    }
}
