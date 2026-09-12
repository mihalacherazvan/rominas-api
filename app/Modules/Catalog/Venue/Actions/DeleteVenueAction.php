<?php

declare(strict_types=1);

namespace Rominas\Catalog\Venue\Actions;

use Rominas\Catalog\Venue\Model\Venue;

class DeleteVenueAction
{
    public function execute(Venue $venue): ?bool
    {
        return $venue->delete();
    }
}
