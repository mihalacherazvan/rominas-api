<?php

declare(strict_types=1);

namespace Rominas\Voting\Enums;

/**
 * Lifecycle of a public voting ballot. `issued` — a single-use link has been sent and not yet used;
 * `submitted` — the voter has cast their ballot (terminal; the link is consumed).
 */
enum BallotStatus: string
{
    case Issued = 'issued';
    case Submitted = 'submitted';

    public function label(): string
    {
        return match ($this) {
            self::Issued => 'Issued',
            self::Submitted => 'Submitted',
        };
    }
}
