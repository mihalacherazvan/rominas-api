<?php

declare(strict_types=1);

namespace Rominas\Catalog\Song\Actions;

use Rominas\Catalog\Song\DataTransferObjects\SongData;
use Rominas\Catalog\Song\Model\Song;

class UpdateSongAction
{
    public function execute(Song $song, SongData $data): Song
    {
        $song->name = $data->name;
        $song->description = $data->description;

        $song->save();

        return $song;
    }
}
