<?php

declare(strict_types=1);

namespace Rominas\Catalog\Band\DataTransferObjects;

class BandData
{
    public function __construct(
        public string $name,
        public ?string $description = null,
    ) {}
}
