<?php

declare(strict_types=1);

namespace Rominas\Editions\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Rominas\Editions\Model\Edition;

class CreateEditionRequest extends FormRequest
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
        // `after:` is strict (>), so chaining each field after the previous enforces
        // starts_at < nominations_start_at < nominations_end_at < voting_start_at
        // < voting_end_at < ends_at.
        return [
            'name' => 'required|string|unique:' . Edition::class . ',name',
            'starts_at' => 'required|date',
            'nominations_start_at' => 'required|date|after:starts_at',
            'nominations_end_at' => 'required|date|after:nominations_start_at',
            'voting_start_at' => 'required|date|after:nominations_end_at',
            'voting_end_at' => 'required|date|after:voting_start_at',
            'ends_at' => 'required|date|after:voting_end_at',
        ];
    }
}
