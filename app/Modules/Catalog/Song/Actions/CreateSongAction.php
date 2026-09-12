<?php

declare(strict_types=1);

namespace Rominas\Catalog\Song\Actions;

use Rominas\Catalog\Song\DataTransferObjects\SongData;
use Rominas\Catalog\Song\Model\Song;

class CreateSongAction
{
    public function execute(SongData $data): Song
    {
        return Song::create([
            'name' => $data->name,
            'description' => $data->description,
        ]);
    }
}
