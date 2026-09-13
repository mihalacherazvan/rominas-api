<?php

declare(strict_types=1);

namespace Rominas\Academy\MemberProposal\Enums;

/**
 * Review state of a member's proposal to add a future academy member. `pending` awaits admin review;
 * `approved` has produced an invited Member; `rejected` was declined.
 */
enum MemberProposalStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Approved => 'Approved',
            self::Rejected => 'Rejected',
        };
    }
}
