<?php

declare(strict_types=1);

namespace Rominas\Editions\Factories;

use Illuminate\Support\Carbon;
use Rominas\Editions\DataTransferObjects\EditionData;
use Rominas\Editions\Requests\CreateEditionRequest;
use Rominas\Editions\Requests\UpdateEditionRequest;

class EditionDataFactory
{
    public static function fromRequest(CreateEditionRequest|UpdateEditionRequest $request): EditionData
    {
        $validated = $request->validated();

        return new EditionData(
            name: $validated['name'],
            starts_at: Carbon::parse($validated['starts_at']),
            nominations_start_at: Carbon::parse($validated['nominations_start_at']),
            nominations_end_at: Carbon::parse($validated['nominations_end_at']),
            voting_start_at: Carbon::parse($validated['voting_start_at']),
            voting_end_at: Carbon::parse($validated['voting_end_at']),
            ends_at: Carbon::parse($validated['ends_at']),
        );
    }
}
