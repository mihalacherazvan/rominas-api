<?php

declare(strict_types=1);

namespace Rominas\Academy\Shortlist\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;
use Rominas\Academy\Shortlist\Model\ShortlistEntry;

class ShortlistEntryResource extends JsonResource
{
    /**
     * @param  Request  $request
     * @return array<string, mixed>|Arrayable<string, mixed>|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        /** @var ShortlistEntry $entry */
        $entry = $this->resource;

        /** @var object{id: int, name: string, slug: string}|null $nominee */
        $nominee = $entry->nominee;

        return [
            'id' => $entry->id,
            'edition_id' => $entry->edition_id,
            'category_id' => $entry->category_id,
            'position' => $entry->position,
            'points' => $entry->points,
            'nominee_type' => $entry->nominee_type->value,
            'nominee_id' => $entry->nominee_id,
            'nominee' => $nominee === null ? null : [
                'id' => $nominee->id,
                'name' => $nominee->name,
                'slug' => $nominee->slug,
            ],
        ];
    }
}
