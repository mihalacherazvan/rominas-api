<?php

declare(strict_types=1);

namespace Rominas\Catalog\Venue\Actions;

use Rominas\Catalog\Venue\DataTransferObjects\VenueData;
use Rominas\Catalog\Venue\Model\Venue;

class UpdateVenueAction
{
    public function execute(Venue $venue, VenueData $data): Venue
    {
        $venue->name = $data->name;
        $venue->description = $data->description;

        $venue->save();

        return $venue;
    }
}
