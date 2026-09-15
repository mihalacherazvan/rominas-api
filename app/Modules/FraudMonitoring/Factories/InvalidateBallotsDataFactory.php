<?php

declare(strict_types=1);

namespace Rominas\FraudMonitoring\Factories;

use Rominas\FraudMonitoring\DataTransferObjects\InvalidateBallotsData;
use Rominas\FraudMonitoring\Requests\InvalidateBallotsRequest;

class InvalidateBallotsDataFactory
{
    public static function fromRequest(InvalidateBallotsRequest $request): InvalidateBallotsData
    {
        /** @var array{reason: string, ballot_ids: list<int|string>} $validated */
        $validated = $request->validated();

        return new InvalidateBallotsData(
            ballotIds: array_map('intval', $validated['ballot_ids']),
            reason: $validated['reason'],
        );
    }
}
