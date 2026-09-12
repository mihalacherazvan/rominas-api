<?php

declare(strict_types=1);

namespace Rominas\Categories\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Rominas\Catalog\Enums\NomineeType;
use Rominas\Categories\Model\Category;
use Rominas\Editions\Model\Edition;

class CreateCategoryRequest extends FormRequest
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
            'edition_id' => 'required|exists:' . Edition::class . ',id',
            'name' => [
                'required',
                'string',
                Rule::unique(Category::class, 'name')->where('edition_id', $this->input('edition_id')),
            ],
            'nominee_type' => ['required', Rule::enum(NomineeType::class)],
            'position' => 'sometimes|integer',
            'description' => 'sometimes|nullable|string',
        ];
    }
}
