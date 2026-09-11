<?php

declare(strict_types=1);

namespace Rominas\Permissions\QueryBuilders;

use Rominas\Permissions\Model\Permission;
use Rominas\Shared\Concerns\QueryBuilderSearchableTrait;
use Rominas\Shared\Concerns\QueryBuilderSortableTrait;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends Builder<Permission>
 */
class PermissionQueryBuilder extends Builder
{
    use QueryBuilderSearchableTrait;
    use QueryBuilderSortableTrait;

    public const PER_PAGE = 10;

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
