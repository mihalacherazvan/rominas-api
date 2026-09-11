<?php

declare(strict_types=1);

namespace Rominas\Roles\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoleRequest extends FormRequest
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
            'associatedPermissions' => 'required|array|min:1',
            'associatedPermissions.*.id' => 'exists:\Spatie\Permission\Models\Permission,id',
        ];
    }
}
