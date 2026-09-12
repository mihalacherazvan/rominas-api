<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Bus;
use Rominas\Academy\Member\Model\Member;
use Rominas\Auth\MagicLink\Jobs\SendMagicLinkJob;
use Rominas\Editions\Enums\EditionStatus;
use Rominas\Editions\Model\Edition;

use function Pest\Laravel\patchJson;
use function Pest\Laravel\postJson;

it('sends invitations to invited members when an edition enters invitations_sent', function (): void {
    actingAsSuperAdmin();
    Bus::fake();

    $edition = Edition::factory()->create(); // draft
    Member::factory()->count(2)->invited()->create();
    Member::factory()->active()->create(); // already active — should not be invited

    patchJson('/api/admin/editions/' . $edition->id . '/status', [
        'status' => EditionStatus::InvitationsSent->value,
    ])->assertStatus(200);

    Bus::assertDispatchedTimes(SendMagicLinkJob::class, 2);
});

it('marks invited members with an invited_at timestamp', function (): void {
    actingAsSuperAdmin();
    Bus::fake();

    $edition = Edition::factory()->create();
    $member = Member::factory()->invited()->create(['invited_at' => null]);

    patchJson('/api/admin/editions/' . $edition->id . '/status', [
        'status' => EditionStatus::InvitationsSent->value,
    ])->assertStatus(200);

    expect($member->refresh()->invited_at)->not->toBeNull();
});

it('sends invitations on demand to every invited member', function (): void {
    actingAsSuperAdmin();
    Bus::fake();

    Member::factory()->count(2)->invited()->create();
    Member::factory()->active()->create(); // already active — should not be invited

    postJson('/api/admin/members/invitations')
        ->assertStatus(200)
        ->assertJsonPath('invited', 2);

    Bus::assertDispatchedTimes(SendMagicLinkJob::class, 2);
});

it('does not send invitations on an unrelated transition', function (): void {
    actingAsSuperAdmin();
    Bus::fake();

    $edition = Edition::factory()->create(['status' => EditionStatus::InvitationsSent]);
    Member::factory()->count(2)->invited()->create();

    patchJson('/api/admin/editions/' . $edition->id . '/status', [
        'status' => EditionStatus::NominationsOpen->value,
    ])->assertStatus(200);

    Bus::assertNotDispatched(SendMagicLinkJob::class);
});
