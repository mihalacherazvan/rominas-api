<?php

declare(strict_types=1);

namespace Rominas\Academy\Actions;

use Rominas\Academy\Member\Enums\MemberStatus;
use Rominas\Academy\Member\Model\Member;
use Rominas\Auth\MagicLink\Jobs\SendMagicLinkJob;

/**
 * Sends a magic-link invitation email to every academy member still awaiting one (`Invited` status).
 * Fired automatically when an edition transitions to `invitations_sent`, and available on demand via
 * `POST /api/admin/members/invitations`. Each invitation is a queued {@see SendMagicLinkJob} on the
 * `member` guard, dispatched after the surrounding transaction commits so the job never races a
 * not-yet-persisted row. Returns the number of members invited.
 */
class SendAcademyInvitationsAction
{
    public function execute(): int
    {
        $members = Member::query()
            ->filterByStatus(MemberStatus::Invited)
            ->get();

        $members->each(function (Member $member): void {
            SendMagicLinkJob::dispatch('member', $member->email, 'academy-invitation-email')
                ->afterCommit();

            $member->forceFill(['invited_at' => now()])->save();
        });

        return $members->count();
    }
}
