<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Sanctum;
use Rominas\Academy\Member\Model\Member;
use Rominas\Academy\MemberProposal\Model\MemberProposal;
use Rominas\Audit\Model\AuditLog;
use Rominas\Audit\Support\AuditHasher;
use Rominas\Catalog\Artist\Model\Artist;
use Rominas\Editions\Model\Edition;
use Rominas\Permissions\Model\Permission;
use Rominas\Roles\Model\Role;
use Rominas\Users\Model\User;

use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\patchJson;
use function Pest\Laravel\postJson;

/**
 * Authenticate as an admin holding the given resource permissions via a role. Roles and permissions are
 * not auto-seeded in tests. Uniquely-named helpers keep this file runnable in isolation.
 *
 * @param  list<string>  $permissions
 */
function auditActingAs(string $role, array $permissions = []): User
{
    Role::query()->firstOrCreate(['name' => $role, 'guard_name' => 'web']);
    $roleModel = Role::query()->where('name', $role)->where('guard_name', 'web')->firstOrFail();

    foreach ($permissions as $permission) {
        Permission::query()->firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        $roleModel->givePermissionTo($permission);
    }

    $user = User::factory()->create();
    $user->assignRole($role);

    Sanctum::actingAs($user);

    return $user;
}

/**
 * Authenticate as a super_admin (bypasses every permission via Gate::before) — the only actor who may read
 * the audit trail by default.
 */
function auditActingAsSuperAdmin(): User
{
    $name = (string) config('permission.super_admin_role');
    Role::query()->firstOrCreate(['name' => $name, 'guard_name' => 'web']);

    $user = User::factory()->create();
    $user->assignRole($name);

    Sanctum::actingAs($user);

    return $user;
}

/**
 * Authenticate as an academy member on the `member` guard.
 */
function auditActingAsMember(): Member
{
    $member = Member::factory()->active()->create();
    Sanctum::actingAs($member, ['member'], 'member');

    return $member;
}

it('records a state-changing admin action with actor, action name and status', function (): void {
    $actor = auditActingAs('admin', ['artists']);

    postJson('/api/admin/artists', ['name' => 'Test Artist'])->assertStatus(201);

    expect(AuditLog::query()->count())->toBe(1);

    $log = AuditLog::query()->firstOrFail();
    expect($log->causer_type)->toBe('user')
        ->and($log->causer_id)->toBe($actor->id)
        ->and($log->causer_label)->toBe($actor->name)
        ->and($log->action)->toBe('api.admin.artists.create')
        ->and($log->method)->toBe('POST')
        ->and($log->status_code)->toBe(201)
        // A top-level create binds no model, so there is no subject.
        ->and($log->subject_type)->toBeNull()
        ->and($log->subject_id)->toBeNull();
});

it('records the bound model as the subject on an update', function (): void {
    auditActingAs('admin', ['artists']);
    $artist = Artist::factory()->create();

    patchJson("/api/admin/artists/{$artist->id}", ['name' => 'Renamed'])->assertStatus(200);

    $log = AuditLog::query()->firstOrFail();
    expect($log->action)->toBe('api.admin.artists.update')
        ->and($log->subject_type)->toBe('artist')
        ->and($log->subject_id)->toBe($artist->id);
});

it('records a sensitive read (viewing final results)', function (): void {
    auditActingAs('custodian', ['results']);
    $edition = Edition::factory()->create();

    // The read is gated by Scoring's "voting closed" state; a fresh edition 422s. Either way the
    // sensitive read is audited with whatever status it returned.
    getJson("/api/admin/editions/{$edition->id}/results");

    $log = AuditLog::query()->firstOrFail();
    expect($log->action)->toBe('api.admin.editions.results.show')
        ->and($log->method)->toBe('GET')
        ->and($log->subject_type)->toBe('edition')
        ->and($log->subject_id)->toBe($edition->id);
});

it('does not record a non-audited read (a plain index)', function (): void {
    auditActingAs('admin', ['artists']);

    getJson('/api/admin/artists')->assertStatus(200);

    expect(AuditLog::query()->count())->toBe(0);
});

it('records a failed action with its status code', function (): void {
    // An admin without the `artists` permission is denied — the denied attempt is still audited.
    auditActingAs('admin', []);

    postJson('/api/admin/artists', ['name' => 'Blocked'])->assertStatus(403);

    $log = AuditLog::query()->firstOrFail();
    expect($log->action)->toBe('api.admin.artists.create')
        ->and($log->status_code)->toBe(403);
});

it('records a validation failure', function (): void {
    auditActingAs('admin', ['artists']);

    postJson('/api/admin/artists', [])->assertStatus(422);

    $log = AuditLog::query()->firstOrFail();
    expect($log->status_code)->toBe(422);
});

it('redacts credentials, tokens and emails from the stored context', function (): void {
    auditActingAs('admin', ['artists']);

    postJson('/api/admin/artists', [
        'name' => 'Keep Me',
        'password' => 'super-secret',
        'api_token' => 'abc123',
        'email' => 'person@example.com',
    ])->assertStatus(201);

    $request = AuditLog::query()->firstOrFail()->context['request'] ?? [];
    expect($request['name'])->toBe('Keep Me')
        ->and($request['password'])->toBe('[redacted]')
        ->and($request['api_token'])->toBe('[redacted]')
        ->and($request['email'])->toBe('[redacted]');
});

it('merges Context-supplied extras into the entry', function (): void {
    auditActingAs('admin', []);

    Route::middleware(['auth:sanctum', 'audit'])
        ->post('/api/admin/__audit_probe', function () {
            Context::add('audit', ['changes' => ['name' => ['old' => 'A', 'new' => 'B']]]);

            return response()->json(['ok' => true]);
        })
        ->name('api.admin.__audit_probe');

    postJson('/api/admin/__audit_probe')->assertStatus(200);

    $context = AuditLog::query()->firstOrFail()->context;
    expect($context['changes']['name']['new'] ?? null)->toBe('B');
});

it('records an admin login, attributing the actor from the response', function (): void {
    $user = User::factory()->create(['email' => 'admin@example.com', 'password' => 'secret-pass']);

    postJson('/api/authenticate', ['email' => 'admin@example.com', 'password' => 'secret-pass'])
        ->assertStatus(200);

    $log = AuditLog::query()->firstOrFail();
    expect($log->action)->toBe('api.authenticate')
        ->and($log->causer_type)->toBe('user')
        ->and($log->causer_id)->toBe($user->id)
        ->and($log->status_code)->toBe(200);
});

it('records a failed admin login as an anonymous attempt with a hashed email', function (): void {
    User::factory()->create(['email' => 'admin@example.com', 'password' => 'secret-pass']);

    postJson('/api/authenticate', ['email' => 'admin@example.com', 'password' => 'wrong'])
        ->assertStatus(401);

    $log = AuditLog::query()->firstOrFail();
    $request = $log->context['request'] ?? [];
    expect($log->action)->toBe('api.authenticate')
        ->and($log->causer_type)->toBeNull()
        ->and($log->causer_id)->toBeNull()
        ->and($log->status_code)->toBe(401)
        // The attempted email is pseudonymized, never stored in plaintext.
        ->and($request['email'])->toBe('[redacted]')
        ->and($request['email_hash'])->toBe(AuditHasher::emailHash('admin@example.com'));
});

it('records an admin logout with the acting user', function (): void {
    $actor = auditActingAs('admin', []);

    postJson('/api/logout')->assertStatus(200);

    $log = AuditLog::query()->firstOrFail();
    expect($log->action)->toBe('api.logout')
        ->and($log->causer_type)->toBe('user')
        ->and($log->causer_id)->toBe($actor->id);
});

it('records a member magic-link request with a hashed email and no actor', function (): void {
    postJson('/api/academy/auth/magic/request', ['email' => 'member@example.com'])
        ->assertStatus(200);

    $log = AuditLog::query()->firstOrFail();
    $request = $log->context['request'] ?? [];
    expect($log->action)->toBe('api.academy.auth.magic.request')
        ->and($log->causer_type)->toBeNull()
        ->and($request['email'])->toBe('[redacted]')
        ->and($request['email_hash'])->toBe(AuditHasher::emailHash('member@example.com'));
});

it('records a member logout with the acting member', function (): void {
    $member = auditActingAsMember();

    postJson('/api/academy/logout')->assertStatus(200);

    $log = AuditLog::query()->firstOrFail();
    expect($log->action)->toBe('api.academy.logout')
        ->and($log->causer_type)->toBe('member')
        ->and($log->causer_id)->toBe($member->id)
        ->and($log->causer_label)->toBe($member->name);
});

it('records a member proposal withdrawal with the member and the proposal subject', function (): void {
    $member = auditActingAsMember();
    $proposal = MemberProposal::factory()->create(['proposed_by_member_id' => $member->id]);

    deleteJson("/api/academy/proposals/{$proposal->id}")->assertStatus(200);

    $log = AuditLog::query()->firstOrFail();
    expect($log->action)->toBe('api.academy.proposals.withdraw')
        ->and($log->causer_type)->toBe('member')
        ->and($log->causer_id)->toBe($member->id)
        ->and($log->subject_type)->toBe('proposal')
        ->and($log->subject_id)->toBe($proposal->id);
});

it('lets a super_admin read the audit trail but forbids others', function (): void {
    AuditLog::factory()->count(3)->create();

    auditActingAs('admin', ['artists']);
    getJson('/api/admin/audit-logs')->assertStatus(403);

    auditActingAsSuperAdmin();
    getJson('/api/admin/audit-logs')
        ->assertStatus(200)
        ->assertJsonCount(3, 'data');
});

it('does not record reads of the audit trail itself', function (): void {
    $log = AuditLog::factory()->create();
    $countBefore = AuditLog::query()->count();

    auditActingAsSuperAdmin();
    getJson('/api/admin/audit-logs')->assertStatus(200);
    getJson("/api/admin/audit-logs/{$log->id}")->assertStatus(200);

    expect(AuditLog::query()->count())->toBe($countBefore);
});
