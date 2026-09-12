# Edition lifecycle

An **edition** is one yearly Rominas awards cycle. It owns a nine-state lifecycle and a six-point
timeline, and at most one edition is ever live. For where `Edition` sits in the schema see
[domain-model.md](domain-model.md#editions).

Everything here lives in `app/Modules/Editions/`.

## 1. The status machine

`EditionStatus` (`Editions/Enums/EditionStatus.php`) is a string-backed enum. Transitions are
**linear**; `archived` is terminal.

```mermaid
stateDiagram-v2
    [*] --> draft
    draft --> invitations_sent
    invitations_sent --> nominations_open
    nominations_open --> nominations_closed
    nominations_closed --> voting_open
    voting_open --> voting_closed
    voting_closed --> committee_review
    committee_review --> results_published
    results_published --> archived
    archived --> [*]
```

The enum owns the rules:

- `allowedTransitions(): list<self>` — the next status(es) reachable from the current one.
- `canTransitionTo(self $target): bool` — whether a move is legal.
- `isActive(): bool` — `true` for everything except `archived`.
- `label(): string` — a human label.

## 2. Changing status

`PATCH /api/admin/editions/{edition}/status` (`can:update,edition`) with `{ "status": "<target>" }`.

`TransitionEditionRequest` validates the target with `Rule::enum(EditionStatus::class)`, then
`TransitionEditionAction` checks `$edition->status->canTransitionTo($target)` — an illegal move
throws a `ValidationException` (**422**). On success it sets and saves the new status.

> **Side-effects are deferred.** Today a transition only moves the status column. The cross-module
> hooks each transition will eventually fire — issuing academy invitations on `invitations_sent`,
> generating the per-category nominee shortlist on `nominations_closed`, opening the public ballot on
> `voting_open`, locking results for the custodian on `committee_review`, publishing on
> `results_published` — land with the `Academy` / `Voting` / `Results` modules.

## 3. The timeline (six datetimes, strictly ordered)

An edition carries six **mandatory** datetimes that must satisfy a strict chain:

```
starts_at  <  nominations_start_at  <  nominations_end_at  <  voting_start_at  <  voting_end_at  <  ends_at
```

| Field | Meaning |
| --- | --- |
| `starts_at` | overall edition window opens |
| `nominations_start_at` | academy nominations open |
| `nominations_end_at` | academy nominations close (the nomination deadline) |
| `voting_start_at` | public voting opens |
| `voting_end_at` | public voting closes |
| `ends_at` | overall edition window closes |

The ordering is enforced in `Create`/`UpdateEditionRequest` by chaining Laravel's `after:` rule
(which is **strict**, `>`) so each field must be strictly after the previous:

```php
'starts_at'            => 'required|date',
'nominations_start_at' => 'required|date|after:starts_at',
'nominations_end_at'   => 'required|date|after:nominations_start_at',
'voting_start_at'      => 'required|date|after:nominations_end_at',
'voting_end_at'        => 'required|date|after:voting_start_at',
'ends_at'              => 'required|date|after:voting_end_at',
```

Because the rule is strict, **no two adjacent boundaries may be equal** — there must be a gap (even a
second) between, say, nominations closing and voting opening. The `status` machine (§1) and this
timeline are currently **independent**: transitions are not auto-gated on the clock. Time-based
gating (e.g. refusing `voting_open` before `voting_start_at`) is a natural future guard.

## 4. One active edition

At most one edition may be **non-archived** at a time (ecosystem CLAUDE.md §5). `CreateEditionAction`
enforces it: if `Edition::query()->active()->exists()` (any status other than `archived`), creating
another is rejected with a `ValidationException` (**422**). To start a new edition, archive the
previous one first.

`EditionQueryBuilder::active()` is the single source of that predicate (`status != archived`).

## 5. CRUD rules

- **Create** → always starts in `draft` (`CreateEditionAction`), subject to the one-active invariant.
- **Update** (`PATCH /api/admin/editions/{edition}`) → edits `name` + the six datetimes (re-validated
  against the strict chain). Status is **not** changed here — use the status endpoint (§2).
- **Delete** → only while `draft` (`DeleteEditionAction` throws 422 otherwise); anything already
  underway is `archived`, never deleted.

## 6. Files

| Concern | File |
| --- | --- |
| Status enum + transition map | `Editions/Enums/EditionStatus.php` |
| Model (casts, slug hook) | `Editions/Model/Edition.php` |
| One-active invariant | `Editions/Actions/CreateEditionAction.php` |
| Guarded transition | `Editions/Actions/TransitionEditionAction.php` |
| Draft-only delete | `Editions/Actions/DeleteEditionAction.php` |
| Timeline validation | `Editions/Requests/{Create,Update}EditionRequest.php` |
| Active predicate | `Editions/QueryBuilders/EditionQueryBuilder.php` |
| Routes | `routes/api/admin/editions.php` |
