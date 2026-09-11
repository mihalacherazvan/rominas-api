<?php

declare(strict_types=1);

namespace Rominas\Taxonomies\QueryBuilders;

use Rominas\Shared\Concerns\QueryBuilderSearchableTrait;
use Rominas\Shared\Concerns\QueryBuilderSortableTrait;
use Rominas\Taxonomies\Model\TaxonomyTerm;
use Rominas\Users\Model\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends Builder<TaxonomyTerm>
 */
class TaxonomyTermQueryBuilder extends Builder
{
    use QueryBuilderSearchableTrait;
    use QueryBuilderSortableTrait;

    public const PER_PAGE = 10;

    public function actionableByUser(User $user, string $action): self
    {
        $taxonomyTermIds = $user->getPermissionTargets('taxonomyTerms', $action);

        if ($taxonomyTermIds->isNotEmpty()) {
            $this->whereIn('id', $taxonomyTermIds->toArray());
        }

        return $this;
    }

    public function assignableByUser(User $user): self
    {
        return $this->actionableByUser($user, 'view');
    }

    public function updatableByUser(User $user): self
    {
        return $this->actionableByUser($user, 'update');
    }

    public function filterByTaxonomyId(int $taxonomyId): self
    {
        return $this->where('taxonomy_id', '=', $taxonomyId);
    }

    public function getOnlyParents(): self
    {
        return $this->whereNull('parent_id')->with('children');
    }

    public function excludeHidden(): self
    {
        return $this->where(function (Builder $query): void {
            $query->whereNull('meta->hidden')
                ->orWhere('meta->hidden', '=', false);
        });
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
