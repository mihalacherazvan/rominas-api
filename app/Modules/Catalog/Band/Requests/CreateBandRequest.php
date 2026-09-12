<?php

declare(strict_types=1);

namespace Rominas\Catalog\Band\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateBandRequest extends FormRequest
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
            'name' => 'required|string',
            'description' => 'sometimes|nullable|string',
        ];
    }
}
