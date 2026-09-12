<?php

declare(strict_types=1);

namespace Rominas\Academy\Member\Enums;

/**
 * Lifecycle of an academy member account. `Invited` members receive a magic-link invitation on the
 * edition's `invitations_sent` transition; a member becomes `Active` on their first successful
 * magic-link login. `Suspended` members are barred from logging in.
 */
enum MemberStatus: string
{
    case Invited = 'invited';
    case Active = 'active';
    case Suspended = 'suspended';

    public function label(): string
    {
        return match ($this) {
            self::Invited => 'Invited',
            self::Active => 'Active',
            self::Suspended => 'Suspended',
        };
    }
}
