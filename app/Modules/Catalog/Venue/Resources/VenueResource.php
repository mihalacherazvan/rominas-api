<?php

declare(strict_types=1);

namespace Rominas\Catalog\Venue\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;
use Rominas\Catalog\Venue\Model\Venue;

class VenueResource extends JsonResource
{
    /**
     * @param  Request  $request
     * @return array<string, mixed>|Arrayable<string, mixed>|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        /** @var Venue $venue */
        $venue = $this->resource;

        return [
            'id' => $venue->id,
            'name' => $venue->name,
            'slug' => $venue->slug,
            'description' => $venue->description,
            'created_at' => $venue->created_at,
            'updated_at' => $venue->updated_at,
        ];
    }
}
