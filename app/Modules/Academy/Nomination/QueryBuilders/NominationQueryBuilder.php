<?php

declare(strict_types=1);

namespace Rominas\Academy\Nomination\QueryBuilders;

use Illuminate\Database\Eloquent\Builder;
use Rominas\Academy\Member\Model\Member;
use Rominas\Academy\Nomination\Model\Nomination;
use Rominas\Editions\Model\Edition;

/**
 * @extends Builder<Nomination>
 */
class NominationQueryBuilder extends Builder
{
    public function forMember(Member $member): self
    {
        return $this->where('member_id', '=', $member->id);
    }

    public function forEdition(Edition $edition): self
    {
        return $this->where('edition_id', '=', $edition->id);
    }
}
