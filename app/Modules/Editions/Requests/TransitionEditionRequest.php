<?php

declare(strict_types=1);

namespace Rominas\Editions\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Rominas\Editions\Enums\EditionStatus;

class TransitionEditionRequest extends FormRequest
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
            'status' => ['required', Rule::enum(EditionStatus::class)],
        ];
    }
}
