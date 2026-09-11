<?php

declare(strict_types=1);

namespace Rominas\Permissions\Controllers;

use Rominas\Permissions\Actions\CreatePermissionAction;
use Rominas\Permissions\Actions\DeletePermissionAction;
use Rominas\Permissions\Actions\UpdatePermissionAction;
use Rominas\Permissions\Factories\PermissionDataFactory;
use Rominas\Permissions\Model\Permission;
use Rominas\Permissions\QueryBuilders\PermissionQueryBuilder;
use Rominas\Permissions\Requests\CreatePermissionRequest;
use Rominas\Permissions\Requests\UpdatePermissionRequest;
use Rominas\Permissions\Resources\PermissionResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

use function response;

class PermissionsController
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return PermissionResource::collection(
            Permission::query()
                ->with('roles')
                ->search($request->query('search'))
                ->orderBy(
                    $request->query('orderBy'),
                    $request->query('orderDir', 'asc'),
                )
                ->paginate($request->query('perPage', PermissionQueryBuilder::PER_PAGE)),
        );
    }

    public function show(Permission $permission): PermissionResource
    {
        return new PermissionResource($permission);
    }

    public function create(CreatePermissionRequest $request, CreatePermissionAction $createPermissionAction): JsonResponse
    {
        $permission = $createPermissionAction->execute(
            PermissionDataFactory::fromRequest($request),
        );

        return (new PermissionResource($permission))->response()->setStatusCode(201);
    }

    public function update(
        Permission $permission,
        UpdatePermissionRequest $request,
        UpdatePermissionAction $updatePermissionAction,
    ): PermissionResource {
        $permission = $updatePermissionAction->execute(
            $permission,
            PermissionDataFactory::fromRequest($request),
        );

        return new PermissionResource($permission);
    }

    public function delete(Permission $permission, DeletePermissionAction $deletePermissionAction): JsonResponse
    {
        $result = $deletePermissionAction->execute($permission);

        return response()->json(['success' => $result]);
    }
}
