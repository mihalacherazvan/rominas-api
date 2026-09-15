# Access control & auth

How Rominas authenticates callers and decides what they may do. Today this covers the **admin /
management** side; the participant (academy member, critic) and public-voter flows are planned (see
[§ Planned](#planned-populations)).

For where `User`/`Role`/`Permission` sit in the schema, see
[domain-model.md](domain-model.md#access-control--users-roles-permissions).

## 1. Populations & guards

Rominas will have several distinct caller populations. The design (from `door`) is **one
authenticatable model + one Sanctum guard per population**, never a single `users` table with a
`type` column.

| Population | Model | Guard | Auth method | Status |
| --- | --- | --- | --- | --- |
| Admin / management | `Rominas\Users\Model\User` | `web` (sanctum default) | username + password → token | **built** |
| Academy member | `Rominas\Academy\Member\Model\Member` | `member` | magic link | **built** (`Academy`) |
| Critic | `Critic` | `critic` | magic link | planned (`CriticsChoice`) |
| Public voter | — (no account) | — (accountless) | single-use link token (no guard) | **built** (`Voting` §2f) |

`config/auth.php` defines the default `web`/sanctum guard over the `users` provider
(`Rominas\Users\Model\User`) and the `member`/sanctum guard over the `members` provider
(`Rominas\Academy\Member\Model\Member`). Each participant guard has its own provider so a token minted
for one population is rejected by another (Sanctum scopes acceptance to the guard's provider model);
this is verified by test — an admin `User` token is rejected by `auth:member`.

## 2b. Member magic-link login (the `Auth\MagicLink` primitive + `Academy`)

Academy members are **passwordless** and log in via a **guard-agnostic** magic-link mechanism in
`app/Modules/Auth/MagicLink/` (reusable by future participant guards such as `critic`):

1. `POST /api/academy/auth/magic/request` (`throttle:magic-request`, `MemberAuthController::requestLink`)
   queues `SendMagicLinkJob('member', email, 'academy-magic-link-email')`. Always **200** — the
   system is **closed** (a link is only actually issued to a known member), so the response never
   leaks whether the address exists.
2. `SendMagicLinkAction` resolves the account through the guard's own auth provider, stores a hashed
   `Str::random(48)` token in `magic_link_tokens` (composite PK `(email, guard)`, 15-min TTL, 60s
   resend cooldown), and sends the email via the `Delivery` pipeline.
3. `POST /api/academy/auth/magic/verify` (`MemberAuthController::verify`) → `VerifyMagicLinkAction`
   validates + **consumes** the token (single-use), returns the `Member`; the controller activates it
   (`status = active`, marks the email verified) and mints `createToken($email, ['member'])`. Response
   `{ memberId, token }`, or **422** on an invalid/expired/consumed token, unknown email, or a
   `suspended` member.
4. Authenticated member endpoints run under `auth:member`: `GET /api/academy/me`, `POST /api/academy/logout`.

**Invitations** run both automatically and on demand, via one shared `SendAcademyInvitationsAction`
(sends the `academy-invitation-email` magic link to every `invited` member):
- **Automatic** — `TransitionEditionAction` fires an `EditionTransitioned` event; the `Academy`
  listener (wired in `EventServiceProvider`) runs the action when an edition enters `invitations_sent`.
- **On demand** — `POST /api/admin/members/invitations` (bulk, returns the count invited) and
  `POST /api/admin/members/{member}/invite` (single member).

Admin roster CRUD lives under `/api/admin/members` (the `members` permission).

## 2c. Ranked nominations (the `Nomination` module)

Authenticated members (guard `member`) rank nominees per category. Ownership is implicit — every
endpoint operates on the caller's own ballot for the single active edition; there is no Spatie policy.
`app/Modules/Academy/Nomination/`:

- `GET /api/academy/nominations` — the whole ballot: every category of the active edition with the
  member's current picks (a synthesized empty draft when none is saved yet; **read-only**, allowed
  even when the window is closed).
- `PUT /api/academy/nominations/categories/{category}` — save/replace up to 5 ranked nominee ids for
  one category (array order = rank). The save/resume unit; creates the draft on first save.
- `POST /api/academy/nominations/submit` — finalize; requires **every** category to hold exactly 5
  picks, then locks the ballot (`submitted`, terminal for the window).

**Deadline gate** (`ResolveOpenNominationEditionAction`): save/submit are allowed only while the
active edition's `status == nominations_open` **and** now ∈ `[nominations_start_at,
nominations_end_at]`. This is the first feature to read the edition datetimes (see
[edition-lifecycle.md](edition-lifecycle.md) §3). **Nominee pool** = the full Catalog of the
category's `nominee_type`; each id is validated to exist in that type's table. Rankings store `rank`
only — the points curve is deferred to `Scoring`.

## 2d. Member proposals (the `MemberProposal` module)

Members propose future academy members; admins review. A standing pool (not edition-scoped),
open anytime. `app/Modules/Academy/MemberProposal/`:

- **Member-facing** (guard `member`, implicit ownership): `POST /api/academy/proposals` (name, email,
  optional reason), `GET /api/academy/proposals` (own only), `DELETE /api/academy/proposals/{proposal}`
  (withdraw own pending). Creating rejects an email that is already a member or already has a pending
  proposal.
- **Admin-facing** (`auth:sanctum` + the `memberProposals` permission via `MemberProposalPolicy`):
  `GET /api/admin/member-proposals` (filter `?status=`), `GET /{memberProposal}`,
  `POST /{memberProposal}/approve`, `POST /{memberProposal}/reject` (both accept an optional `note`).
  **Approve creates an invited Member** from the proposal (reusing `CreateMemberAction`) and links it
  (`member_id`), so the new member flows into the invitation mechanism (§2b).

## 2e. Nominee shortlist (the `Shortlist` module)

Admin-only, `app/Modules/Academy/Shortlist/`. Generation is an **explicit admin action** (no
`EditionTransitioned` listener). All routes are `auth:sanctum` and gated on the new `shortlists`
permission via `ShortlistEntryPolicy` — checked against the `ShortlistEntry` class (`can:create,…` /
`can:viewAny,…`), since the routes bind an `edition`/`category`, not a shortlist instance:

- `GET  /api/admin/editions/{edition}/shortlist` — review the edition's generated shortlist (`can:viewAny`).
- `POST /api/admin/editions/{edition}/shortlist` — bulk-generate every category in the edition (`can:create`).
- `POST /api/admin/editions/{edition}/categories/{category}/shortlist` — generate one category (`can:create`).
- `GET  /api/admin/editions/{edition}/categories/{category}/shortlist/candidates` — the full ranked
  candidate pool (every academy-nominated nominee, ordered by points) for the manual review UI (`can:viewAny`).
- `PUT  /api/admin/editions/{edition}/categories/{category}/shortlist` — the **manual adjust**: replace a
  category's shortlist with the admin's final ordered nominees (≤ 5, distinct) (`can:update`).

The generate and adjust write endpoints **422** unless the edition is `nominations_closed` (nominations
frozen, voting not yet open); this same guard is the regeneration/adjust lock (re-running or adjusting
while `nominations_closed` replaces the category's entries; once voting opens, the shortlist is frozen).
`shortlists` is granted to `admin` in `PermissionSeeder`. See the [Shortlist](domain-model.md#shortlist)
domain notes for the entity and the `Rominas\Scoring\RankPoints` points curve.

## 2f. Public voting (the `Voting` module)

Fully **accountless** — there is no guard, no Sanctum, no account. Possession of a one-time link token is
the authorization, checked in application code. All endpoints are public (no auth middleware), mounted
under `/api/voting` (mirroring the public `academy/auth.php` group):

- `POST /api/voting/request` (`throttle:voting-request`) — request a link by email. Always a generic 200;
  the response never reveals eligibility or prior voting. **One link, ever, per email per edition**
  (a repeat request issues nothing). A closed voting window surfaces as a 422.
- `GET /api/voting/ballot?token=…` — load the ballot the token authorizes: the edition's shortlist to
  rank, grouped by category.
- `POST /api/voting/ballot` — cast the ballot once (`{token, categories:[{category_id, nominees:[ids in
  rank order]}]}`). Each included category must rank **all** its shortlisted nominees exactly once; ≥1
  category required. On success the ranks are stored and the link is consumed (single-use, terminal).

**Gating** mirrors the academy open-window pattern: `ResolveOpenVotingEditionAction` requires the active
edition to be `voting_open` with now inside `[voting_start_at, voting_end_at]`. Token resolution
(`ResolveBallotByTokenAction`) accepts only an `issued`, unexpired ballot and yields a single generic 422
on any failure (missing / used / expired — no distinction), like the academy magic-link verify.

**GDPR**: no plaintext personal data is stored. `email_hash`/`ip_hash` are HMAC-SHA256 (`VoterHasher`)
keyed by `config('voting.pepper')` (env `VOTING_PEPPER`, falls back to `APP_KEY`) — the pepper must be set
and never rotated once ballots exist. The link token is stored only as its SHA-256. See the
[Voting](domain-model.md#voting) domain notes.

## 2g. Results (the `Results` module)

Final results are **custodian-gated until publication**. Authorization is the `results` permission via
`ResultPolicy` (checked against the `ResultSnapshot` class — the routes bind an `Edition`, not a snapshot
row); only the `custodian` role holds it (`super_admin` bypasses). The `admin` role is **deliberately not**
granted `results` — an admin cannot see complete results before they are public.

Admin/custodian endpoints, under `/api/admin/editions` (Sanctum-guarded):

- `GET /api/admin/editions/{edition}/results` (`can:viewAny,ResultSnapshot`) — the edition's complete
  results. Computed live during the review window, served from the frozen snapshot once published.
- `GET /api/admin/editions/{edition}/results/export` (`can:viewAny,ResultSnapshot`) — the same data as a
  CSV download (`response()->streamDownload()`, no extra dependency).

Both inherit Scoring's gate: results are only available once public voting has closed
(`voting_closed` / `committee_review` / `results_published`), otherwise a 422 on `status`.

Public endpoint, **unauthenticated**, under `/api/results`:

- `GET /api/results/editions/{edition}` — a **published** edition's results, served from the frozen
  snapshot only. An edition without a snapshot (not yet published) is a **404**; results become public
  only at publish time.

The snapshot is frozen automatically by the `FreezeResultsOnEditionPublished` listener when the edition
transitions to `results_published` (see [edition-lifecycle.md](edition-lifecycle.md)). See the
[Results](domain-model.md#results) domain notes.

## 2. Admin login (the `Auth` module)

`POST /api/authenticate` (`Auth\Controllers\AuthController::authenticate`, `AuthenticateRequest`):

1. Credentials are validated statelessly with `Auth::guard('web')->once($credentials)` — this only
   leverages Laravel's password-hash check; **no session is persisted** (the API is token-based).
2. On success a Sanctum personal-access token is minted: `$user->createToken($email)` — carrying the
   default `['*']` abilities. The response is `{ userId, token }`.
3. `POST /api/logout` (`auth:sanctum`) revokes the current token.

> **Token abilities are not used for authorization here.** Admin authorization runs entirely through
> policies / `$user->can()` (see below); nothing calls `tokenCan()` or Sanctum's ability middleware,
> so the token carries `['*']`. Ability-scoping is reserved for the planned participant/voter tokens,
> where least-privilege on a one-time link genuinely matters.

## 3. Roles & permissions (spatie)

Authorization is [spatie/laravel-permission](https://spatie.be/docs/laravel-permission). `Role` and
`Permission` subclass the Spatie models (`app/Modules/{Roles,Permissions}/Model/`).

**Seeded roles** (`RoleSeeder`): `super_admin`, `admin`, `custodian`, `fraud_monitor`.
**Custodian is a role, not a user type** — a custodian is an admin with the extra right to view/export
final results before publication (the `results` permission, granted only to `custodian`; see §2g).

**Permissions** (`PermissionSeeder`) are resource-named: `editions`, `categories`, `members`,
`memberProposals`, `shortlists`, `results`, `artists`, `bands`, `venues`, `songs`, `albums`,
`taxonomies`, `taxonomyTerms` (plus `roles`, `permissions`).
The `admin` role is granted the domain set **except `results`**; `custodian` is granted `results`;
`super_admin` needs none (it bypasses — §5). User/role/permission management is currently
`super_admin`-only. Regenerate/extend the set as modules land.

## 4. Policies & route authorization

Every model declares its policy with the native attribute `#[UsePolicy(XPolicy::class)]`; routes
authorize inline with `can:` middleware:

```php
Route::get('/', [ArtistsController::class, 'index'])
    ->middleware('can:viewAny,' . Artist::class);
Route::patch('/{artist}', [ArtistsController::class, 'update'])
    ->middleware('can:update,artist');
```

The simple policies gate on a plain resource permission, e.g. `ArtistPolicy::viewAny` →
`$user->can('artists')`. Row-level scoping is applied in the query builders via
`visibleToUser()`/`actionableByUser()` (which call `getPermissionTargets()`, §6).

## 5. The super-admin bypass

`app/Providers/AuthServiceProvider.php` registers a `Gate::before` hook:

```php
Gate::before(static fn (User $user) => $user->hasRole(config('permission.super_admin_role')) ? true : null);
```

A `Gate::before` callback runs ahead of every ability/policy check: returning `true` allows, `null`
falls through to normal resolution. So a `super_admin` passes every `can:` check without holding any
explicit permission. (`config('permission.super_admin_role')` = `super_admin`.)

## 6. Wildcard permissions & permission-target encoding

Wildcard permissions are **enabled** (`config/permission.php` → `enable_wildcard_permission = true`,
`wildcard_permission = App\Auth\Spatie\Permission\WildcardPermission::class`). Our class extends
Spatie's base `WildcardPermission` and adds support for **comma-delimited sub-parts** in the
`checkIndex` step (e.g. `users.update.editor,moderator` grants the action only when every listed
sub-part matches).

**How `$user->can('artists')` reaches spatie** (no override of `can()` — it's the Gate):

1. `User` extends `Illuminate\Foundation\Auth\User` → `Authorizable::can()` delegates to the Gate.
2. Spatie's `PermissionServiceProvider` registers a second `Gate::before` (guarded by
   `register_permission_check_method`) that calls `$user->checkPermissionTo($ability)`.
3. `checkPermissionTo` → `hasPermissionTo`, which (wildcard on) runs `hasWildcardPermission` — whose
   index is built from `getAllPermissions()` (direct **+** role-derived). So role-granted permissions
   satisfy `->can()`.

Our own super-admin `Gate::before` is registered too; multiple `before` callbacks run in order —
ours grants everything for `super_admin`, otherwise returns `null` and lets spatie's hook (then the
policy) decide.

**Permission-target encoding** — `Permissions\Concerns\HasPermissionTargets` (a trait on `User`)
decodes permission *names* into the set of targets a user may act on:
`getPermissionTargets($resource, $action)` reads names like `users.view.editor` or `users.*.editor`
and returns the target names/ids. The module query builders use it for row-level visibility
(`actionableByUser()`). A plain `resource` permission (no target) grants all rows. Rominas' current
resources use the simple plain-permission form; the target machinery is available for finer-grained
control (e.g. per-role user management) when needed.

## Planned populations

- **Critic** — a separate authenticatable model + `critic` Sanctum guard, reusing the same
  guard-agnostic magic-link mechanism (§2b) by passing `'critic'`. Built with the `CriticsChoice` module.

(The **public voter** population is now **built** — see §2f.)
