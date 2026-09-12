<?php

declare(strict_types=1);

namespace Rominas\Catalog\Song\DataTransferObjects;

class SongData
{
    public function __construct(
        public string $name,
        public ?string $description = null,
    ) {}
}
