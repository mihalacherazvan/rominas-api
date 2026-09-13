<?php

declare(strict_types=1);

namespace Rominas\Academy\MemberProposal\Factories;

use Rominas\Academy\MemberProposal\DataTransferObjects\MemberProposalData;
use Rominas\Academy\MemberProposal\Requests\CreateMemberProposalRequest;

class MemberProposalDataFactory
{
    public static function fromCreateRequest(CreateMemberProposalRequest $request): MemberProposalData
    {
        $validated = $request->validated();

        return new MemberProposalData(
            name: $validated['name'],
            email: $validated['email'],
            reason: $validated['reason'] ?? null,
        );
    }
}
