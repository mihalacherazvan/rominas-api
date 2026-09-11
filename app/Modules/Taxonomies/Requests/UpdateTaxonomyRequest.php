<?php

declare(strict_types=1);

namespace Rominas\Taxonomies\Requests;

use Rominas\Taxonomies\Model\Taxonomy;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaxonomyRequest extends FormRequest
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
        /** @var Taxonomy $taxonomy */
        $taxonomy = $this->route('taxonomy');

        return [
            'name' => ['required', 'string', Rule::unique($taxonomy->getTable())->ignore($taxonomy)],
            'hierarchical' => 'required|boolean',
            'allows_multiple' => 'required|boolean',
        ];
    }
}
