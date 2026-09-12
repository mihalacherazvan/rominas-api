<?php

declare(strict_types=1);

namespace Rominas\Catalog\Band\Actions;

use Rominas\Catalog\Band\DataTransferObjects\BandData;
use Rominas\Catalog\Band\Model\Band;

class CreateBandAction
{
    public function execute(BandData $data): Band
    {
        return Band::create([
            'name' => $data->name,
            'description' => $data->description,
        ]);
    }
}
