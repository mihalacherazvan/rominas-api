<?php

declare(strict_types=1);

namespace Rominas\Catalog\Album\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;
use Rominas\Catalog\Album\Model\Album;

class AlbumResource extends JsonResource
{
    /**
     * @param  Request  $request
     * @return array<string, mixed>|Arrayable<string, mixed>|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        /** @var Album $album */
        $album = $this->resource;

        return [
            'id' => $album->id,
            'name' => $album->name,
            'slug' => $album->slug,
            'description' => $album->description,
            'created_at' => $album->created_at,
            'updated_at' => $album->updated_at,
        ];
    }
}
