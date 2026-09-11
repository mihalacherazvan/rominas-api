<?php

declare(strict_types=1);

namespace Rominas\Taxonomies\Requests;

use Rominas\Taxonomies\Model\Taxonomy;
use Illuminate\Foundation\Http\FormRequest;

class CreateTaxonomyRequest extends FormRequest
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
            'name' => 'required|string|unique:' . Taxonomy::class,
            'hierarchical' => 'required|boolean',
            'allows_multiple' => 'required|boolean',
        ];
    }
}
