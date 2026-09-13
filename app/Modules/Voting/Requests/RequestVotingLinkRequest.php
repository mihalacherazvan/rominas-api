<?php

declare(strict_types=1);

namespace Rominas\Voting\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequestVotingLinkRequest extends FormRequest
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
            'email' => 'required|email',
        ];
    }
}
