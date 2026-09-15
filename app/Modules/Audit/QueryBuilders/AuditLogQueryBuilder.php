<?php

declare(strict_types=1);

namespace Rominas\Audit\QueryBuilders;

use Illuminate\Database\Eloquent\Builder;
use Rominas\Audit\Model\AuditLog;
use Rominas\Users\Model\User;

/**
 * @extends Builder<AuditLog>
 */
class AuditLogQueryBuilder extends Builder
{
    public const PER_PAGE = 25;

    public function actionableByUser(User $user, string $action): self
    {
        $ids = $user->getPermissionTargets('audit', $action);

        if ($ids->isNotEmpty()) {
            $this->whereIn('id', $ids->toArray());
        }

        return $this;
    }

    public function visibleToUser(User $user): self
    {
        return $this->actionableByUser($user, 'view');
    }

    public function forCauser(string $type, int $id): self
    {
        return $this->where('causer_type', '=', $type)->where('causer_id', '=', $id);
    }

    public function forAction(string $action): self
    {
        return $this->where('action', '=', $action);
    }

    public function forSubject(string $type, int $id): self
    {
        return $this->where('subject_type', '=', $type)->where('subject_id', '=', $id);
    }

    public function betweenDates(?string $from, ?string $to): self
    {
        if ($from !== null) {
            $this->where('created_at', '>=', $from);
        }

        if ($to !== null) {
            $this->where('created_at', '<=', $to);
        }

        return $this;
    }
}
