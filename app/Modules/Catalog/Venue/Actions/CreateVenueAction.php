<?php

declare(strict_types=1);

namespace Rominas\Catalog\Venue\Actions;

use Rominas\Catalog\Venue\DataTransferObjects\VenueData;
use Rominas\Catalog\Venue\Model\Venue;

class CreateVenueAction
{
    public function execute(VenueData $data): Venue
    {
        return Venue::create([
            'name' => $data->name,
            'description' => $data->description,
        ]);
    }
}
