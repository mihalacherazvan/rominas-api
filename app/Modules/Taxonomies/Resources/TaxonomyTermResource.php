<?php

declare(strict_types=1);

namespace Rominas\Taxonomies\Resources;

use Rominas\Taxonomies\Model\TaxonomyTerm;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class TaxonomyTermResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array<string, mixed>|Arrayable<string, mixed>|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        /** @var TaxonomyTerm $taxonomyTerm */
        $taxonomyTerm = $this->resource;

        return [
            'id' => $taxonomyTerm->id,
            'name' => $taxonomyTerm->name,
            'slug' => $taxonomyTerm->slug,
            'parent_id' => $taxonomyTerm->parent_id,
            'meta' => $taxonomyTerm->meta,
            'taxonomy' => new TaxonomyResource($this->whenLoaded('taxonomy')),
            /**
             * TODO: Consider retrieving children with a dedicated query
             * in order to apply user `taxonomyTerms` permission restrictions.
             */
            'children' => TaxonomyTermResource::collection($this->whenLoaded('children')),
            'created_at' => $taxonomyTerm->created_at,
            'updated_at' => $taxonomyTerm->updated_at,
        ];
    }
}
