<?php

declare(strict_types=1);

namespace Rominas\Academy\Nomination\Enums;

/**
 * State of a member's nomination ballot for an edition. A `draft` ballot is saveable/resumable while
 * nominations are open; `submitted` is terminal for the window (locked once finalized).
 */
enum NominationStatus: string
{
    case Draft = 'draft';
    case Submitted = 'submitted';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Submitted => 'Submitted',
        };
    }
}
