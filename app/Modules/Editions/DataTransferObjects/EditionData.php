<?php

declare(strict_types=1);

namespace Rominas\Editions\DataTransferObjects;

use Illuminate\Support\Carbon;

class EditionData
{
    public function __construct(
        public string $name,
        public Carbon $starts_at,
        public Carbon $nominations_start_at,
        public Carbon $nominations_end_at,
        public Carbon $voting_start_at,
        public Carbon $voting_end_at,
        public Carbon $ends_at,
    ) {}
}
