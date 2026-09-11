<?php

declare(strict_types=1);

namespace Rominas\Taxonomies\Model;

use Rominas\Taxonomies\Meta\TaxonomyTermMeta;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * @implements CastsAttributes<TaxonomyTermMeta|null, TaxonomyTermMeta|array<string, mixed>|null>
 */
class TaxonomyTermMetaCast implements CastsAttributes
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ?TaxonomyTermMeta
    {
        if (empty($value)) {
            return null;
        }

        return new TaxonomyTermMeta(
            ...json_decode((string) $value, true, 512, JSON_THROW_ON_ERROR),
        );
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if (empty($value)) {
            return null;
        }

        return json_encode($value, JSON_THROW_ON_ERROR);
    }
}
