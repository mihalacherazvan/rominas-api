<?php

declare(strict_types=1);

namespace Rominas\Users\Resources;

use Rominas\Permissions\Resources\PermissionResource;
use Rominas\Roles\Resources\RoleResource;
use Rominas\Users\Model\User;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array<string, mixed>|Arrayable<string, mixed>|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        /** @var User $user */
        $user = $this->resource;

        return [
            'id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'roles' => RoleResource::collection($user->roles),
            'permissions' => PermissionResource::collection($user->getAllPermissions()),
        ];
    }
}
