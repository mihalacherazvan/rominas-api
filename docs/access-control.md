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
| Academy member | `Member` | `member` | magic link / OTP | planned (`Academy`) |
| Critic | `Critic` | `critic` | magic link / OTP | planned (`CriticsChoice`) |
| Public voter | — (no account) | — | single-use signed link | planned (`Voting`) |

`config/auth.php` currently defines the default `web`/sanctum guard over the `users` provider
(`Rominas\Users\Model\User`). Each planned participant guard gets its own provider so a token minted
for one population is rejected by another (Sanctum scopes acceptance to the guard's provider model).

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
final results (that gate lands with the `Results` module).

**Permissions** (`PermissionSeeder`) are resource-named: `editions`, `categories`, `artists`,
`bands`, `venues`, `songs`, `albums`, `taxonomies`, `taxonomyTerms` (plus `roles`, `permissions`).
The `admin` role is granted the domain set; `super_admin` needs none (it bypasses — §5).
User/role/permission management is currently `super_admin`-only. Regenerate/extend the set as
modules land.

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

- **Academy member / Critic** — separate authenticatable models + Sanctum guards, sharing one
  **guard-agnostic magic-link/OTP mechanism** (hashed-token tables + Actions + queued Job envelopes,
  reusing the `Delivery` module). Built alongside the `Academy` module.
- **Public voter** — no account: a pseudonymized hashed-email record + a single-use, signed,
  expiring link. Built with the `Voting` module.
