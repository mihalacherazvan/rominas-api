<?php

declare(strict_types=1);

namespace Rominas\FraudMonitoring\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;
use Rominas\FraudMonitoring\Model\FraudAlert;

/**
 * A fraud alert for the admin: the finding, its triage status, and (on show) the implicated ballots
 * rendered as hashes only via MonitoredBallotResource. `context` holds hashes/counts only — no plaintext PII.
 */
class FraudAlertResource extends JsonResource
{
    /**
     * @param  Request  $request
     * @return array<string, mixed>|Arrayable<string, mixed>|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        /** @var FraudAlert $alert */
        $alert = $this->resource;

        return [
            'id' => $alert->id,
            'edition_id' => $alert->edition_id,
            'type' => $alert->type->value,
            'severity' => $alert->severity->value,
            'status' => $alert->status->value,
            'ballot_count' => $alert->ballot_count,
            'context' => $alert->context,
            'first_detected_at' => $alert->first_detected_at->toISOString(),
            'last_detected_at' => $alert->last_detected_at->toISOString(),
            'ballots' => MonitoredBallotResource::collection($this->whenLoaded('ballots')),
        ];
    }
}
