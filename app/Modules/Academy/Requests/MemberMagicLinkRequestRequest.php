<?php

declare(strict_types=1);

namespace Rominas\Academy\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MemberMagicLinkRequestRequest extends FormRequest
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
