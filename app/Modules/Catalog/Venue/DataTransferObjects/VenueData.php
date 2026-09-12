<?php

declare(strict_types=1);

namespace Rominas\Catalog\Venue\DataTransferObjects;

class VenueData
{
    public function __construct(
        public string $name,
        public ?string $description = null,
    ) {}
}
