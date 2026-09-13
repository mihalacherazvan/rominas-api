<?php

declare(strict_types=1);

namespace Rominas\Voting\Actions;

use Illuminate\Validation\ValidationException;
use Rominas\Editions\Enums\EditionStatus;
use Rominas\Editions\Model\Edition;

/**
 * Resolves the single active edition the public may vote in, enforcing the window gate: voting is open
 * only when that edition's status is `voting_open` AND now falls within `[voting_start_at, voting_end_at]`.
 * Throws a 422 otherwise. Mirrors ResolveOpenNominationEditionAction for the voting phase.
 */
class ResolveOpenVotingEditionAction
{
    public function execute(): Edition
    {
        $edition = Edition::query()->active()->first();

        if ($edition === null || $edition->status !== EditionStatus::VotingOpen) {
            throw ValidationException::withMessages([
                'voting' => 'Voting is not open.',
            ]);
        }

        $now = now();

        if ($now->lt($edition->voting_start_at) || $now->gt($edition->voting_end_at)) {
            throw ValidationException::withMessages([
                'voting' => 'The voting window is closed.',
            ]);
        }

        return $edition;
    }
}
