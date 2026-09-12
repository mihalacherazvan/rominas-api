<?php

declare(strict_types=1);

namespace Rominas\Editions\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;
use Rominas\Editions\Model\Edition;

class EditionResource extends JsonResource
{
    /**
     * @param  Request  $request
     * @return array<string, mixed>|Arrayable<string, mixed>|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        /** @var Edition $edition */
        $edition = $this->resource;

        return [
            'id' => $edition->id,
            'name' => $edition->name,
            'slug' => $edition->slug,
            'starts_at' => $edition->starts_at,
            'nominations_start_at' => $edition->nominations_start_at,
            'nominations_end_at' => $edition->nominations_end_at,
            'voting_start_at' => $edition->voting_start_at,
            'voting_end_at' => $edition->voting_end_at,
            'ends_at' => $edition->ends_at,
            'status' => $edition->status->value,
            'status_label' => $edition->status->label(),
            'created_at' => $edition->created_at,
            'updated_at' => $edition->updated_at,
        ];
    }
}
