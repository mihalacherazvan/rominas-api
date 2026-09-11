<?php

declare(strict_types=1);

namespace Rominas\Taxonomies\Resources;

use Rominas\Taxonomies\Model\Taxonomy;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class TaxonomyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array<string, mixed>|Arrayable<string, mixed>|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        /** @var Taxonomy $taxonomy */
        $taxonomy = $this->resource;

        return [
            'id' => $taxonomy->id,
            'name' => $taxonomy->name,
            'hierarchical' => $taxonomy->hierarchical,
            'allows_multiple' => $taxonomy->allows_multiple,
            'created_at' => $taxonomy->created_at,
            'updated_at' => $taxonomy->updated_at,
        ];
    }
}
