<?php

declare(strict_types=1);

namespace Rominas\Results\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;
use Rominas\Results\Support\EditionResultsPresenter;
use Rominas\Scoring\DataTransferObjects\EditionScore;

/**
 * Serializes an edition's results (a Scoring {@see EditionScore}, whether computed live or rebuilt from a
 * frozen snapshot) into the public results shape, enriched with category and nominee display names by
 * {@see EditionResultsPresenter}.
 */
class EditionResultsResource extends JsonResource
{
    /**
     * @param  Request  $request
     * @return array<string, mixed>|Arrayable<string, mixed>|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        /** @var EditionScore $score */
        $score = $this->resource;

        return (new EditionResultsPresenter())->present($score);
    }
}
