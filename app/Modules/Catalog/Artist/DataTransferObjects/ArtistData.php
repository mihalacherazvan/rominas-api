<?php

declare(strict_types=1);

namespace Rominas\Catalog\Artist\DataTransferObjects;

class ArtistData
{
    public function __construct(
        public string $name,
        public ?string $description = null,
    ) {}
}
