<?php

declare(strict_types=1);

namespace Rominas\Categories\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Rominas\Catalog\Enums\NomineeType;
use Rominas\Categories\Model\Category;

class UpdateCategoryRequest extends FormRequest
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
        /** @var Category $category */
        $category = $this->route('category');

        return [
            'name' => [
                'required',
                'string',
                Rule::unique($category->getTable(), 'name')
                    ->where('edition_id', $category->edition_id)
                    ->ignore($category),
            ],
            'nominee_type' => ['required', Rule::enum(NomineeType::class)],
            'position' => 'sometimes|integer',
            'description' => 'sometimes|nullable|string',
        ];
    }
}
