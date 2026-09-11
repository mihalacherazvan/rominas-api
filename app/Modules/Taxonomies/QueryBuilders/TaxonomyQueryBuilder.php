<?php

declare(strict_types=1);

namespace Rominas\Taxonomies\QueryBuilders;

use Rominas\Shared\Concerns\QueryBuilderSearchableTrait;
use Rominas\Shared\Concerns\QueryBuilderSortableTrait;
use Rominas\Taxonomies\Model\Taxonomy;
use Rominas\Users\Model\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends Builder<Taxonomy>
 */
class TaxonomyQueryBuilder extends Builder
{
    use QueryBuilderSearchableTrait;
    use QueryBuilderSortableTrait;

    public const PER_PAGE = 10;

    public function actionableByUser(User $user, string $action): self
    {
        $taxonomyIds = $user->getPermissionTargets('taxonomies', $action);

        if ($taxonomyIds->isNotEmpty()) {
            $this->whereIn('id', $taxonomyIds->toArray());
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
            'created_at',
            'updated_at',
        ];
    }
}
