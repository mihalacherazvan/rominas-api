<?php

declare(strict_types=1);

namespace Rominas\Academy\Listeners;

use Rominas\Academy\Actions\SendAcademyInvitationsAction;
use Rominas\Editions\Enums\EditionStatus;
use Rominas\Editions\Events\EditionTransitioned;

/**
 * When an edition enters `invitations_sent`, send magic-link invitations to every awaiting academy
 * member. Wired in EventServiceProvider.
 */
class SendAcademyInvitationsOnEditionTransitioned
{
    public function __construct(
        private readonly SendAcademyInvitationsAction $action,
    ) {}

    public function handle(EditionTransitioned $event): void
    {
        if ($event->to !== EditionStatus::InvitationsSent) {
            return;
        }

        $this->action->execute();
    }
}
