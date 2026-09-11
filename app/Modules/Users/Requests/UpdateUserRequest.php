<?php

declare(strict_types=1);

namespace Rominas\Users\Requests;

use Rominas\Roles\Model\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
        $roleIds = Role::query()
            ->creatableByUser($this->user())
            ->get()
            ->implode('id', ',');

        return [
            'name' => 'required|string',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($this->route('user'))],
            'newPassword' => 'sometimes|confirmed',
            'associatedRoles' => 'required|array|size:1',
            'associatedRoles.*.id' => 'in:' . $roleIds,
        ];
    }
}
