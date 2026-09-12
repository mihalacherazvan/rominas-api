<?php

declare(strict_types=1);

namespace Rominas\Academy\Member\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Rominas\Academy\Member\Model\Member;

class CreateMemberRequest extends FormRequest
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
            'email' => 'required|email|unique:' . Member::class . ',email',
        ];
    }
}
