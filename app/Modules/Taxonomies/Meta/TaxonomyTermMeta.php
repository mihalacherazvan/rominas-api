<?php

declare(strict_types=1);

namespace Rominas\Taxonomies\Meta;

class TaxonomyTermMeta
{
    /** @var array<string, mixed>|null */
    public ?array $seo = null;

    public ?bool $hidden = null;

    /**
     * @param  array<string, mixed>|null  $seo
     */
    public function __construct(
        ?array $seo = null,
        ?bool $hidden = false,
    ) {
        if ($seo) {
            $this->seo = $seo;
        }

        if ($hidden) {
            $this->hidden = true;
        }
    }
}
