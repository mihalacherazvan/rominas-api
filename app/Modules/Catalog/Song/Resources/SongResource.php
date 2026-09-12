<?php

declare(strict_types=1);

namespace Rominas\Catalog\Song\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;
use Rominas\Catalog\Song\Model\Song;

class SongResource extends JsonResource
{
    /**
     * @param  Request  $request
     * @return array<string, mixed>|Arrayable<string, mixed>|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        /** @var Song $song */
        $song = $this->resource;

        return [
            'id' => $song->id,
            'name' => $song->name,
            'slug' => $song->slug,
            'description' => $song->description,
            'created_at' => $song->created_at,
            'updated_at' => $song->updated_at,
        ];
    }
}
