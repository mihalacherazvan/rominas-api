<?php

declare(strict_types=1);

namespace Rominas\Permissions\Concerns;

use Illuminate\Support\Collection;
use Spatie\Permission\Models\Permission;

trait HasPermissionTargets
{
    /**
     * @return Collection<int, string>
     */
    public function getPermissionTargets(string $resource, string $action): Collection
    {
        return $this->getAllPermissions()
            ->filter(
                fn(Permission $permission) => str_contains($permission->name, "$resource.$action")
                    || str_contains($permission->name, "$resource.*.")
                    || $permission->name === $resource,
            )->map(function (Permission $permission) use ($resource, $action) {
                if ($permission->name === $resource || $permission->name === "$resource.$action") {
                    return false;
                }

                $permissionTargets = [];
                $permissionName = $permission->name;

                if (str_contains($permissionName, "$resource.*.")) {
                    $rawPermissionTarget = str_replace("$resource.*.", '', $permissionName);

                    /**
                     * Pass full permission target value if there is an additional filter in it
                     * so that it can be further processed in the query builder,
                     * otherwise split it into the actual targets (usually because there's only one use case)
                     */
                    if (str_contains($rawPermissionTarget, ':')) {
                        $permissionTargets = $rawPermissionTarget;
                    } else {
                        $permissionTargets = explode(',', $rawPermissionTarget);
                    }
                }

                if (str_contains($permissionName, "$resource.$action.")) {
                    $rawPermissionTarget = str_replace("$resource.$action.", '', $permissionName);

                    /**
                     * Pass full permission target value if there is an additional filter in it
                     * so that it can be further processed in the query builder,
                     * otherwise split it into the actual targets (usually because there's only one use case)
                     */
                    if (str_contains($rawPermissionTarget, ':')) {
                        $permissionTargets = $rawPermissionTarget;
                    } else {
                        $permissionTargets = explode(',', $rawPermissionTarget);
                    }
                }

                return $permissionTargets;
            })
            ->filter(fn($targets) => $targets)
            ->flatten();
    }
}
