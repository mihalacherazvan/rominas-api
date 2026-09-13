<?php

declare(strict_types=1);

namespace Rominas\Voting\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitBallotRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Structural validation only. Per-category rules — a category must exist in the edition and rank all
     * of its shortlisted nominees exactly once — are dynamic (they depend on the shortlist size) and live
     * in SubmitBallotAction.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'token' => 'required|string',
            'categories' => 'required|array|min:1',
            'categories.*.category_id' => 'required|integer',
            'categories.*.nominees' => 'required|array|min:1',
            'categories.*.nominees.*' => 'integer|distinct',
        ];
    }
}
