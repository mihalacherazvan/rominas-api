<?php

declare(strict_types=1);

namespace Rominas\Academy\MemberProposal\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Admin approve/reject payload — an optional free-text review note.
 */
class ReviewMemberProposalRequest extends FormRequest
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
            'note' => 'sometimes|nullable|string',
        ];
    }
}
