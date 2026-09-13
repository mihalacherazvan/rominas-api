<?php

declare(strict_types=1);

namespace Rominas\Voting\Actions;

use Illuminate\Support\Str;
use Rominas\Voting\Enums\BallotStatus;
use Rominas\Voting\Jobs\SendVotingLinkJob;
use Rominas\Voting\Model\Ballot;
use Rominas\Voting\Support\VoterHasher;

/**
 * Issues a one-time voting link for an email address. Requires voting to be open. The voter is
 * pseudonymized to an `email_hash`; the link is a high-entropy token stored only as its SHA-256.
 *
 * One link, ever, per email: if a ballot already exists for this edition + email, nothing is issued and
 * we return silently, so the caller can always answer a generic 200 without revealing whether the address
 * has already requested or voted. The plaintext token is handed to the queued delivery job and never stored.
 */
class RequestVotingLinkAction
{
    public function __construct(
        private readonly ResolveOpenVotingEditionAction $resolveOpenVoting,
    ) {}

    public function execute(string $email): void
    {
        $edition = $this->resolveOpenVoting->execute();

        $emailHash = VoterHasher::emailHash($email);

        if (Ballot::query()->forEdition($edition)->byEmailHash($emailHash)->exists()) {
            return;
        }

        $token = Str::random(48);

        Ballot::query()->create([
            'edition_id' => $edition->id,
            'email_hash' => $emailHash,
            'token_hash' => hash('sha256', $token),
            'status' => BallotStatus::Issued,
            'expires_at' => $edition->voting_end_at,
        ]);

        SendVotingLinkJob::dispatch($email, $token)->afterCommit();
    }
}
