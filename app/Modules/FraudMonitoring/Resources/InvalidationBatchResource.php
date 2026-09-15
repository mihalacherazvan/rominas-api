<?php

declare(strict_types=1);

namespace Rominas\FraudMonitoring\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;
use Rominas\FraudMonitoring\Model\InvalidationBatch;

/**
 * A vote-cancellation batch: the reason, the acting admin, when it happened, and how many ballots it
 * cancelled. `ballots_count` is expected to be loaded (withCount/loadCount) by the caller.
 */
class InvalidationBatchResource extends JsonResource
{
    /**
     * @param  Request  $request
     * @return array<string, mixed>|Arrayable<string, mixed>|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        /** @var InvalidationBatch $batch */
        $batch = $this->resource;

        return [
            'id' => $batch->id,
            'edition_id' => $batch->edition_id,
            'reason' => $batch->reason,
            'invalidated_by' => $batch->invalidated_by,
            'ballots_count' => (int) ($batch->getAttribute('ballots_count') ?? 0),
            'created_at' => $batch->created_at?->toISOString(),
        ];
    }
}
