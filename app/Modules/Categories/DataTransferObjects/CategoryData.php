<?php

declare(strict_types=1);

namespace Rominas\Categories\DataTransferObjects;

use Rominas\Catalog\Enums\NomineeType;

class CategoryData
{
    public function __construct(
        public int $edition_id,
        public string $name,
        public NomineeType $nominee_type,
        public int $position = 0,
        public ?string $description = null,
    ) {}
}
