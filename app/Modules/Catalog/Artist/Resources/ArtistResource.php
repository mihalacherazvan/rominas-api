<?php

declare(strict_types=1);

namespace Rominas\Catalog\Artist\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;
use Rominas\Catalog\Artist\Model\Artist;

class ArtistResource extends JsonResource
{
    /**
     * @param  Request  $request
     * @return array<string, mixed>|Arrayable<string, mixed>|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        /** @var Artist $artist */
        $artist = $this->resource;

        return [
            'id' => $artist->id,
            'name' => $artist->name,
            'slug' => $artist->slug,
            'description' => $artist->description,
            'created_at' => $artist->created_at,
            'updated_at' => $artist->updated_at,
        ];
    }
}
