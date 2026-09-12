<?php

declare(strict_types=1);

namespace Rominas\Catalog\Band\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;
use Rominas\Catalog\Band\Model\Band;

class BandResource extends JsonResource
{
    /**
     * @param  Request  $request
     * @return array<string, mixed>|Arrayable<string, mixed>|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        /** @var Band $band */
        $band = $this->resource;

        return [
            'id' => $band->id,
            'name' => $band->name,
            'slug' => $band->slug,
            'description' => $band->description,
            'created_at' => $band->created_at,
            'updated_at' => $band->updated_at,
        ];
    }
}
