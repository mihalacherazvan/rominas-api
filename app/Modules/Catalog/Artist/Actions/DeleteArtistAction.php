<?php

declare(strict_types=1);

namespace Rominas\Catalog\Artist\Actions;

use Rominas\Catalog\Artist\Model\Artist;

class DeleteArtistAction
{
    public function execute(Artist $artist): ?bool
    {
        return $artist->delete();
    }
}
