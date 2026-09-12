<?php

declare(strict_types=1);

namespace Rominas\Catalog\Song\Actions;

use Rominas\Catalog\Song\Model\Song;

class DeleteSongAction
{
    public function execute(Song $song): ?bool
    {
        return $song->delete();
    }
}
