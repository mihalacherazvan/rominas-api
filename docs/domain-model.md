# Domain Model

Every **persistent entity** in `rominas-api`, grouped by module, with its key fields and the
relationships between entities. Models live at `app/Modules/<Module>/…/Model/`.

Only **implemented** modules are documented in full; planned modules are listed at the end so the
picture stays complete. Keep this file in sync whenever an entity is added or changed.

## Module overview

| Module | Entities | Kind |
| --- | --- | --- |
| [Access control](#access-control--users-roles-permissions) (`Users`, `Roles`, `Permissions`) | User, Role, Permission | Admin accounts & authorization |
| [Taxonomies](#taxonomies) | Taxonomy, TaxonomyTerm | Classification vocabulary |
| [Editions](#editions) | Edition | Yearly award edition + lifecycle |
| [Categories](#categories) | Category | Award categories, scoped to an edition |
| [Catalog](#catalog) | Artist, Band, Venue, Song, Album | Nominatable entities |
| [Academy](#academy) | Member, Nomination, NominationRanking | Participant accounts + ranked nominations |
| [Behavioural modules](#behavioural-modules-no-persistent-entities) (`Auth`, `Delivery`, `Shared`) | MagicLinkToken | Behaviour + the magic-link token store |

```mermaid
erDiagram
    USER }o--o{ ROLE : "roles"
    ROLE }o--o{ PERMISSION : "permissions"
    USER }o--o{ PERMISSION : "direct permissions"

    TAXONOMY ||--o{ TAXONOMY_TERM : "contains"
    TAXONOMY_TERM ||--o{ TAXONOMY_TERM : "parent/children"

    EDITION ||--o{ CATEGORY : "has"
    CATEGORY }o--|| NOMINEE_TYPE : "accepts (enum)"
    NOMINEE_TYPE }o--|| CATALOG : "artist|band|venue|song|album"
```

> **Catalog entities are standalone** — there are no relationships between Artist/Band/Venue/Song/
> Album (nor to Categories) yet. A category names the *type* of catalog entity it accepts via the
> `NomineeType` enum; the actual nominee↔category links arrive with the `Academy`/`Voting` modules.

---

## Access control — `Users`, `Roles`, `Permissions`

Admin accounts and authorization, built on [spatie/laravel-permission](https://spatie.be/docs/laravel-permission)
and Laravel Sanctum. `Role` and `Permission` extend the Spatie base models; `User` is the
authenticatable admin account. The full auth story (guards, tokens, the wildcard-permission scheme)
is in [access-control.md](access-control.md).

```mermaid
erDiagram
    USER }o--o{ ROLE : "assigned — model_has_roles"
    ROLE }o--o{ PERMISSION : "grants — role_has_permissions"
    USER }o--o{ PERMISSION : "direct — model_has_permissions"
```

**User** — `app/Modules/Users/Model/User.php`

| Field | Notes |
| --- | --- |
| `name`, `email`, `password` | `password` is `hashed`; `email` unique |
| `email_verified_at` | nullable datetime |

Traits: `HasRoles`, `HasApiTokens` (Sanctum), `HasPermissionTargets`, `Notifiable`. `isSuperAdmin()`
checks the configured `super_admin_role`, which bypasses every gate via `Gate::before`
(`app/Providers/AuthServiceProvider.php`).

**Role** — `app/Modules/Roles/Model/Role.php` — Spatie role (`name`, `guard_name`). Seeded set:
`super_admin`, `admin`, `custodian`, `fraud_monitor` (`database/seeders/RoleSeeder.php`).
**Permission** — `app/Modules/Permissions/Model/Permission.php` — Spatie permission (`name`,
`guard_name`).

**Relationships**

- `User` ⇄ `Role` (many-to-many, pivot `model_has_roles`).
- `Role` ⇄ `Permission` (many-to-many, pivot `role_has_permissions`).
- `User` ⇄ `Permission` (direct, many-to-many, pivot `model_has_permissions`).

---

## Taxonomies

A reusable classification vocabulary. A **Taxonomy** (e.g. *Genre*, *Region*, *Tag*) owns a set of
**TaxonomyTerms**; terms may be hierarchical and carry SEO/visibility metadata. Ported from `door`.

> **Not yet attached to anything.** Catalog entities carry no taxonomy pivots in the current phase —
> genre/region tagging of artists/songs/venues will be added when the domain calls for it.

```mermaid
erDiagram
    TAXONOMY ||--o{ TAXONOMY_TERM : "contains"
    TAXONOMY_TERM ||--o{ TAXONOMY_TERM : "parent → children"
```

**Taxonomy** — `app/Modules/Taxonomies/Model/Taxonomy.php`

| Field | Notes |
| --- | --- |
| `name` | unique |
| `hierarchical` | bool — whether terms may nest |
| `allows_multiple` | bool — whether an associated model may hold one term or many |

**TaxonomyTerm** — `app/Modules/Taxonomies/Model/TaxonomyTerm.php`

| Field | Notes |
| --- | --- |
| `taxonomy_id` | FK → Taxonomy (`cascadeOnDelete`) |
| `parent_id` | nullable self-FK (`nullOnDelete`) |
| `name`, `slug` | unique **per taxonomy** (`(taxonomy_id, name)` / `(taxonomy_id, slug)`); slug auto-generated |
| `meta` | JSON, cast to a `TaxonomyTermMeta` value object (`seo`, `hidden`) |

**Relationships** — `Taxonomy` → many `TaxonomyTerm` (`terms()`); `TaxonomyTerm` → one `Taxonomy`
(`taxonomy()`), self-referencing `parent()` / `children()`.

---

## Editions

A yearly awards edition and its lifecycle. Only **one non-archived edition** may exist at a time.
The lifecycle state machine and the six-datetime timeline are documented in full in
[edition-lifecycle.md](edition-lifecycle.md).

**Edition** — `app/Modules/Editions/Model/Edition.php`

| Field | Notes |
| --- | --- |
| `name` | unique |
| `slug` | unique, auto-generated from `name` |
| `starts_at` | overall edition window start (mandatory) |
| `nominations_start_at` | academy nominations open (mandatory) |
| `nominations_end_at` | academy nominations close (mandatory) |
| `voting_start_at` | public voting opens (mandatory) |
| `voting_end_at` | public voting closes (mandatory) |
| `ends_at` | overall edition window end (mandatory) |
| `status` | `EditionStatus` enum, default `draft` |

All six datetimes are **strictly ordered**: `starts_at < nominations_start_at < nominations_end_at
< voting_start_at < voting_end_at < ends_at`, enforced by chained `after:` rules in
`Create`/`UpdateEditionRequest`.

**Enum** — `EditionStatus` (`Editions/Enums`): `draft` → `invitations_sent` → `nominations_open`
→ `nominations_closed` → `voting_open` → `voting_closed` → `committee_review` → `results_published`
→ `archived` (linear; `archived` is terminal). `isActive()` = not `archived`.

**Relationships** — `Edition` → many `Category` (via `Category::edition()`).

**Invariant** — at most one edition with `status != archived`, enforced in `CreateEditionAction`
(422 on a second active edition).

---

## Categories

Award categories (e.g. *Best Album*), scoped to an edition and **typed**: each declares which
catalog entity type it accepts.

**Category** — `app/Modules/Categories/Model/Category.php`

| Field | Notes |
| --- | --- |
| `edition_id` | FK → Edition (`cascadeOnDelete`) |
| `name`, `slug` | unique **per edition** (`(edition_id, name)` / `(edition_id, slug)`); slug auto-generated |
| `nominee_type` | `NomineeType` enum — the catalog entity type this category competes |
| `position` | int — display ordering |
| `description` | nullable |

**Enum** — `NomineeType` (`Catalog/Enums/NomineeType.php`): `artist` \| `band` \| `venue` \| `song`
\| `album`. Slug-backed (never the class FQN); `modelClass()` resolves the slug to its Eloquent
model (morph-map style) and `label()` gives a display name.

**Relationships** — `Category` → one `Edition` (`edition()`). The `nominee_type` links a category to
a catalog **model class** (not a row) via `NomineeType::modelClass()`.

**Rules** — `edition_id` is required on create and **immutable on update** (`CategoryDataFactory`
has separate `fromCreateRequest`/`fromUpdateRequest`); `nominee_type` validated with
`Rule::enum(NomineeType::class)`. Points-per-rank / scoring config is **not** on Category — it
belongs to the planned `Scoring` module.

---

## Catalog

The nominatable entities. Each is a **flat, standalone** per-entity submodule (`Catalog/Artist/…`,
`Catalog/Band/…`, …) with the same skeleton; there are **no relationships between them yet**
(no band membership, no song→album) and no taxonomy pivots.

Every entity shares the same shape:

| Field | Notes |
| --- | --- |
| `name` | required |
| `slug` | unique, auto-generated from `name` (`booted()` saving hook) |
| `description` | nullable |

**Entities** — `Artist`, `Band`, `Venue`, `Song`, `Album` (`app/Modules/Catalog/<Entity>/Model/`).
Tables: `artists`, `bands`, `venues`, `songs`, `albums`. Each has full CRUD under `/admin/<entity>s`,
a policy gating on the `<entity>s` permission, and a typed QueryBuilder (search/sort + permission
scoping). The `NomineeType` enum (see [Categories](#categories)) maps its slugs to these models.

> Type-specific scalar fields (e.g. Venue `city`, Album `release_year`) and inter-entity
> relationships are intentionally deferred — add them when a concrete requirement lands.

---

## Academy

Academy-member accounts, their passwordless auth, and their **ranked nominations**. A **Member** is a
participant account on its own `member` Sanctum guard (never the admin `web` guard); members log in
via magic link. Admins manage the roster; invitations are emailed when an edition enters
`invitations_sent`. Members then rank nominees per category. Member proposals and the voting
shortlist are still planned (see the table below).

```mermaid
erDiagram
    MEMBER ||--o{ MAGIC_LINK_TOKEN : "authenticates via (email, guard=member)"
    MEMBER ||--o{ NOMINATION : "ballots"
    EDITION ||--o{ NOMINATION : "scopes"
    NOMINATION ||--o{ NOMINATION_RANKING : "ranked picks"
    CATEGORY ||--o{ NOMINATION_RANKING : "within"
    NOMINATION_RANKING }o--|| CATALOG : "nominee (morph by NomineeType slug)"
```

**Member** — `app/Modules/Academy/Member/Model/Member.php`

| Field | Notes |
| --- | --- |
| `name` | required |
| `email` | unique |
| `status` | `MemberStatus` enum — `invited` \| `active` \| `suspended` |
| `email_verified_at` | nullable; set on first magic-link login |
| `invited_at` | nullable; set when an invitation is sent |
| `activated_at` | nullable; set on first successful login |

Authenticatable (`HasApiTokens`, `Notifiable`); **passwordless** (no password column). `#[UsePolicy(MemberPolicy)]`
gates admin-side CRUD on the `members` permission. `MemberQueryBuilder` adds search/sort +
`filterByStatus()`. A member becomes `active` on first magic-link verify; `suspended` members are
barred from logging in. Full auth story in [access-control.md](access-control.md).

**Nomination** — `app/Modules/Academy/Nomination/Model/Nomination.php` — a member's ballot for one
edition (one row per member+edition).

| Field | Notes |
| --- | --- |
| `member_id` | FK → Member (`cascadeOnDelete`) |
| `edition_id` | FK → Edition (`cascadeOnDelete`) |
| `status` | `NominationStatus` enum — `draft` \| `submitted` |
| `submitted_at` | nullable; set when finalized |

Unique `(member_id, edition_id)`. `belongsTo` Member/Edition, `hasMany` rankings.

**NominationRanking** — `app/Modules/Academy/Nomination/Model/NominationRanking.php` — one ranked pick.

| Field | Notes |
| --- | --- |
| `nomination_id` | FK → Nomination (`cascadeOnDelete`) |
| `category_id` | FK → Category (`cascadeOnDelete`) |
| `rank` | tinyint, 1 = top (order → points, later, in `Scoring`) |
| `nominee_type` | `NomineeType` enum slug — the polymorphic morph alias |
| `nominee_id` | the Catalog row id |

Unique `(nomination_id, category_id, rank)` and `(nomination_id, category_id, nominee_type,
nominee_id)`. `nominee()` is a `morphTo` resolved via the **morph map** (`AppServiceProvider`, mapping
each `NomineeType` slug → its Catalog model, so nominee rows store the slug not a FQN). Members rank
from the **full Catalog** of the category's type; gating (open window) and the submit rule (every
category has exactly 5) live in the Nomination actions — see the Academy nominations flow in
[access-control.md](access-control.md#2c-ranked-nominations-the-nomination-module).

**MagicLinkToken** — `app/Modules/Auth/MagicLink/Model/MagicLinkToken.php` (guard-agnostic; see the
Behavioural modules note).

---

## Behavioural modules (no persistent entities)

- **`Auth`** (`app/Modules/Auth/`) — admin username/password login → Sanctum token
  (`POST /api/authenticate`), and logout (see [access-control.md](access-control.md)). Also hosts the
  **guard-agnostic magic-link primitive** (`Auth/MagicLink/`): the `MagicLinkToken` model (table
  `magic_link_tokens`, composite PK `(email, guard)`, hashed single-use token, 15-min TTL, 60s resend
  cooldown) plus `SendMagicLinkAction`/`VerifyMagicLinkAction` (resolve the account through the
  guard's own auth provider, so any participant guard reuses them) and a queued `SendMagicLinkJob`.
- **`Delivery`** (`app/Modules/Delivery/`) — transactional email behind a transport seam
  (SMTP active; Brevo ported as an opt-in alternative). An `action → PayloadFactory → service`
  pipeline driven by `config/delivery.php`; no stored models.
- **`Shared`** (`app/Modules/Shared/Concerns/`) — cross-module query-builder concerns
  (`QueryBuilderSearchableTrait`, `QueryBuilderSortableTrait`).

---

## Planned modules (not yet implemented)

Tracked in the ecosystem [`CLAUDE.md`](../../CLAUDE.md); each gets its own section here once built.

| Module | Planned entities / concern |
| --- | --- |
| `Academy` (remaining) | Member proposals, and the per-category **nominee shortlist** that advances to public voting. (`Member` + magic-link auth + invitations + ranked `Nomination`s are **built** — see [Academy](#academy).) |
| `CriticsChoice` | `Critic` (separate authenticatable + guard), committee submissions |
| `Voting` | Accountless public voting — pseudonymized hashed-email + single-use signed link; multi-step ballot; one submission per person |
| `Scoring` | Ranking→points rules; academy 60% / public 40% weighting; points-per-rank curve |
| `Results` | Custodian-gated aggregation + export |
| `FraudMonitoring` | Near-real-time vote monitoring; vote cancellation (reason required, audited) |
| `Audit` | Audit trail across sensitive actions (greenfield — no `door` template) |
