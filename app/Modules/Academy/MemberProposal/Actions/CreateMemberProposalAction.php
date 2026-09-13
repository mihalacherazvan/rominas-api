<?php

declare(strict_types=1);

namespace Rominas\Academy\MemberProposal\Actions;

use Illuminate\Validation\ValidationException;
use Rominas\Academy\Member\Model\Member;
use Rominas\Academy\MemberProposal\DataTransferObjects\MemberProposalData;
use Rominas\Academy\MemberProposal\Enums\MemberProposalStatus;
use Rominas\Academy\MemberProposal\Model\MemberProposal;

/**
 * Records a member's proposal of a future academy member. Rejects an email that already belongs to a
 * Member, or that already has a pending proposal (avoid duplicates).
 */
class CreateMemberProposalAction
{
    public function execute(Member $proposer, MemberProposalData $data): MemberProposal
    {
        if (Member::query()->where('email', $data->email)->exists()) {
            throw ValidationException::withMessages([
                'email' => 'This person is already an academy member.',
            ]);
        }

        $pendingExists = MemberProposal::query()
            ->where('email', $data->email)
            ->filterByStatus(MemberProposalStatus::Pending)
            ->exists();

        if ($pendingExists) {
            throw ValidationException::withMessages([
                'email' => 'There is already a pending proposal for this person.',
            ]);
        }

        return MemberProposal::create([
            'proposed_by_member_id' => $proposer->id,
            'name' => $data->name,
            'email' => $data->email,
            'reason' => $data->reason,
            'status' => MemberProposalStatus::Pending,
        ]);
    }
}
