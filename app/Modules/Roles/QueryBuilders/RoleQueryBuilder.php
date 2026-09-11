<?php

declare(strict_types=1);

namespace Rominas\Roles\QueryBuilders;

use Rominas\Roles\Model\Role;
use Rominas\Shared\Concerns\QueryBuilderSearchableTrait;
use Rominas\Shared\Concerns\QueryBuilderSortableTrait;
use Rominas\Users\Model\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends Builder<Role>
 */
class RoleQueryBuilder extends Builder
{
    use QueryBuilderSearchableTrait;
    use QueryBuilderSortableTrait;

    public const PER_PAGE = 10;

    public function actionableByUser(User $user, string $action): self
    {
        $rolesAccessibleByUser = $user->getPermissionTargets('users', $action);

        if ($rolesAccessibleByUser->isNotEmpty()) {
            $this->whereIn(
                'name',
                $rolesAccessibleByUser->toArray(),
            );
        }

        return $this;
    }

    public function visibleToUser(User $user): self
    {
        return $this->actionableByUser($user, 'view');
    }

    public function creatableByUser(User $user): self
    {
        return $this->actionableByUser($user, 'create');
    }

    /**
     * @return string[]
     */
    protected function getSearchableFields(): array
    {
        return [
            'name',
        ];
    }

    /**
     * @return string[]
     */
    protected function getSortableFields(): array
    {
        return [
            'id',
            'name',
        ];
    }
}
