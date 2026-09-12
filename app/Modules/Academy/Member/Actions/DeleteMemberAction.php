<?php

declare(strict_types=1);

namespace Rominas\Academy\Member\Actions;

use Rominas\Academy\Member\Model\Member;

class DeleteMemberAction
{
    public function execute(Member $member): bool
    {
        return (bool) $member->delete();
    }
}
