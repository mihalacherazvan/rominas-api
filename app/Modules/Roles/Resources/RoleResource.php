<?php

declare(strict_types=1);

namespace Rominas\Roles\Resources;

use Rominas\Permissions\Resources\PermissionResource;
use Rominas\Roles\Model\Role;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class RoleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array<string, mixed>|Arrayable<string, mixed>|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        /** @var Role $role */
        $role = $this->resource;

        return [
            'id' => $role->id,
            'name' => $role->name,
            'permissions' => PermissionResource::collection($role->getAllPermissions()),
        ];
    }
}
