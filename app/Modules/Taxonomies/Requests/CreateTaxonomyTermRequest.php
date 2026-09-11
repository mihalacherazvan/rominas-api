<?php

declare(strict_types=1);

namespace Rominas\Taxonomies\Requests;

use Rominas\Taxonomies\Model\Taxonomy;
use Rominas\Taxonomies\Model\TaxonomyTerm;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateTaxonomyTermRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                Rule::unique(TaxonomyTerm::class, 'name')
                    ->where('taxonomy_id', $this->input('taxonomy_id')),
            ],
            'taxonomy_id' => 'required|exists:' . Taxonomy::class . ',id',
            'parentId' => 'sometimes|exists:' . TaxonomyTerm::class . ',id',

            'meta' => 'sometimes|nullable|array:seo,hidden',
            'meta.hidden' => 'sometimes|boolean',
            'meta.seo' => 'sometimes|array:title,description,keywords,robots,frontendTitle,frontendDescription',
            'meta.seo.title' => 'sometimes|string',
            'meta.seo.description' => 'sometimes|string',
            'meta.seo.keywords' => 'sometimes|string',
            'meta.seo.robots' => 'sometimes|array:noindex,nofollow',
            'meta.seo.robots.noindex' => 'sometimes|boolean',
            'meta.seo.robots.nofollow' => 'sometimes|boolean',
            'meta.seo.frontendTitle' => 'sometimes|string',
            'meta.seo.frontendDescription' => 'sometimes|string',
        ];
    }
}
