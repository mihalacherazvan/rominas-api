<?php

declare(strict_types=1);

namespace Rominas\Roles\Controllers;

use Rominas\Roles\Actions\CreateRoleAction;
use Rominas\Roles\Actions\DeleteRoleAction;
use Rominas\Roles\Actions\UpdateRoleAction;
use Rominas\Roles\Factories\RoleDataFactory;
use Rominas\Roles\Model\Role;
use Rominas\Roles\QueryBuilders\RoleQueryBuilder;
use Rominas\Roles\Requests\CreateRoleRequest;
use Rominas\Roles\Requests\UpdateRoleRequest;
use Rominas\Roles\Resources\RoleResource;
use Rominas\Users\Model\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

use function response;

class RolesController
{
    public function index(Request $request): AnonymousResourceCollection
    {
        /** @var User $user */
        $user = $request->user();

        return RoleResource::collection(
            Role::query()
                ->with('permissions')
                ->visibleToUser($user)
                ->search($request->query('search'))
                ->orderBy(
                    $request->query('orderBy'),
                    $request->query('orderDir', 'asc'),
                )
                ->paginate($request->query('perPage', RoleQueryBuilder::PER_PAGE)),
        );
    }

    public function show(Role $role): RoleResource
    {
        return new RoleResource($role);
    }

    public function create(CreateRoleRequest $request, CreateRoleAction $createRoleAction): JsonResponse
    {
        $role = $createRoleAction->execute(
            RoleDataFactory::fromRequest($request),
        );

        return (new RoleResource($role))->response()->setStatusCode(201);
    }

    public function update(Role $role, UpdateRoleRequest $request, UpdateRoleAction $updateRoleAction): RoleResource
    {
        $role = $updateRoleAction->execute(
            $role,
            RoleDataFactory::fromRequest($request),
        );

        return new RoleResource($role);
    }

    public function delete(Role $role, DeleteRoleAction $deleteRoleAction): JsonResponse
    {
        $result = $deleteRoleAction->execute($role);

        return response()->json(['success' => $result]);
    }
}
