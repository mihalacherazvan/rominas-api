<?php

declare(strict_types=1);

namespace Rominas\Academy\Member\Actions;

use Rominas\Academy\Member\DataTransferObjects\MemberData;
use Rominas\Academy\Member\Model\Member;

class CreateMemberAction
{
    public function execute(MemberData $data): Member
    {
        return Member::create([
            'name' => $data->name,
            'email' => $data->email,
            'status' => $data->status,
        ]);
    }
}
