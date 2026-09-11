<?php

declare(strict_types=1);

namespace Rominas\Users\QueryBuilders;

use Rominas\Shared\Concerns\QueryBuilderSearchableTrait;
use Rominas\Shared\Concerns\QueryBuilderSortableTrait;
use Rominas\Users\Model\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends Builder<User>
 */
class UserQueryBuilder extends Builder
{
    use QueryBuilderSearchableTrait;
    use QueryBuilderSortableTrait;

    public const PER_PAGE = 10;

    public function actionableByUser(User $user, string $action): self
    {
        if ($user->isSuperAdmin()) {
            return $this;
        }

        $roleTargets = $user->getPermissionTargets('users', $action);

        if ($roleTargets->isNotEmpty()) {
            $this->whereHas('roles', function (Builder $query) use ($roleTargets) {
                $query->whereIn('roles.name', $roleTargets->toArray());
            });
        }

        return $this;
    }

    public function visibleToUser(User $user): self
    {
        return $this->actionableByUser($user, 'view');
    }

    public function updatableByUser(User $user): self
    {
        return $this->actionableByUser($user, 'update');
    }

    public function deletableByUser(User $user): self
    {
        return $this->actionableByUser($user, 'delete');
    }

    /**
     * @return string[]
     */
    protected function getSearchableFields(): array
    {
        return [
            'name',
            'email',
        ];
    }

    /**
     * @return string[]
     */
    protected function getSortableFields(): array
    {
        return [
            'id',
            'email',
            'name',
            'created_at',
            'updated_at',
        ];
    }
}
