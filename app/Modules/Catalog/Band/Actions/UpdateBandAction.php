<?php

declare(strict_types=1);

namespace Rominas\Catalog\Band\Actions;

use Rominas\Catalog\Band\DataTransferObjects\BandData;
use Rominas\Catalog\Band\Model\Band;

class UpdateBandAction
{
    public function execute(Band $band, BandData $data): Band
    {
        $band->name = $data->name;
        $band->description = $data->description;

        $band->save();

        return $band;
    }
}
