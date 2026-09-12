<?php

declare(strict_types=1);

namespace Rominas\Academy\Nomination\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveCategoryRankingRequest extends FormRequest
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
        // An empty array clears the category. Nominee existence + type are validated in the action,
        // which has the Category (and thus its nominee_type) in hand.
        return [
            'nominees' => 'present|array|max:5',
            'nominees.*' => 'integer|distinct',
        ];
    }
}
