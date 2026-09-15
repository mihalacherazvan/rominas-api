<?php

declare(strict_types=1);

namespace Rominas\FraudMonitoring\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * A batch vote-cancellation: a mandatory reason and the ids of the ballots to invalidate. Eligibility
 * (submitted, not already invalidated, belonging to the edition) is enforced in the action.
 */
class InvalidateBallotsRequest extends FormRequest
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
            'reason' => 'required|string|min:3|max:1000',
            'ballot_ids' => 'required|array|min:1',
            'ballot_ids.*' => 'integer|distinct|min:1',
        ];
    }
}
