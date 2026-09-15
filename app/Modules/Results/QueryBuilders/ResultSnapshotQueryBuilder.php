<?php

declare(strict_types=1);

namespace Rominas\Results\QueryBuilders;

use Illuminate\Database\Eloquent\Builder;
use Rominas\Editions\Model\Edition;
use Rominas\Results\Model\ResultSnapshot;
use Rominas\Users\Model\User;

/**
 * @extends Builder<ResultSnapshot>
 */
class ResultSnapshotQueryBuilder extends Builder
{
    public const PER_PAGE = 25;

    public function actionableByUser(User $user, string $action): self
    {
        $ids = $user->getPermissionTargets('results', $action);

        if ($ids->isNotEmpty()) {
            $this->whereIn('id', $ids->toArray());
        }

        return $this;
    }

    public function visibleToUser(User $user): self
    {
        return $this->actionableByUser($user, 'view');
    }

    public function forEdition(Edition $edition): self
    {
        return $this->where('edition_id', '=', $edition->id);
    }
}
