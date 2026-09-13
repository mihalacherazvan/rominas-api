<?php

declare(strict_types=1);

namespace Rominas\Voting\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Rominas\Delivery\Actions\DeliveryAction;

/**
 * Thin async envelope that emails a voting link off the request thread. Carries the recipient email and
 * the plaintext token (needed to build the link URL); neither is persisted — the ballot row stores only
 * hashes. The token is already stored (hashed) by RequestVotingLinkAction before this job is dispatched.
 */
class SendVotingLinkJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly string $email,
        private readonly string $token,
    ) {}

    public function handle(DeliveryAction $delivery): void
    {
        $delivery->execute('voting-link-email', ['email'], $this->email, $this->token);
    }
}
