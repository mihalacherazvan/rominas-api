<?php

declare(strict_types=1);

namespace Rominas\Permissions\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePermissionRequest extends FormRequest
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
            'associatedRoles' => 'sometimes|array',
            'associatedRoles.*.id' => 'exists:\Spatie\Permission\Models\Role,id',
        ];
    }
}
