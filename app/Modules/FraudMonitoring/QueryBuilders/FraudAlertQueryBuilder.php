<?php

declare(strict_types=1);

namespace Rominas\FraudMonitoring\QueryBuilders;

use Illuminate\Database\Eloquent\Builder;
use Rominas\Editions\Model\Edition;
use Rominas\FraudMonitoring\Model\FraudAlert;
use Rominas\Users\Model\User;

/**
 * @extends Builder<FraudAlert>
 */
class FraudAlertQueryBuilder extends Builder
{
    public const PER_PAGE = 25;

    public function actionableByUser(User $user, string $action): self
    {
        $ids = $user->getPermissionTargets('fraudMonitoring', $action);

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
