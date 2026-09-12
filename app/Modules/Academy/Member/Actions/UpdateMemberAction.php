<?php

declare(strict_types=1);

namespace Rominas\Academy\Member\Actions;

use Rominas\Academy\Member\DataTransferObjects\MemberData;
use Rominas\Academy\Member\Model\Member;

class UpdateMemberAction
{
    public function execute(Member $member, MemberData $data): Member
    {
        $member->update([
            'name' => $data->name,
            'email' => $data->email,
            'status' => $data->status,
        ]);

        return $member;
    }
}
