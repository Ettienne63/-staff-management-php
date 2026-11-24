# Architecture

How the app is put together today, and where its rules are enforced. `docs/PRODUCT_PLAN.md` says *what* the app must do; this file says *where in the code* it happens. Update it when a change moves one of those places.

## Shape

One Laravel 13 app at the repository root, server-rendered with Blade and Tailwind (built by Vite). No API routes yet: everything is in `routes/web.php`. SQLite locally; production storage is undecided.

```
HTTP request
  → route group in routes/web.php (guest | auth | internal | schools/{school})
  → middleware (auth, throttle, EnsurePlatformAdmin or EnsureSchoolAccess)
  → controller (role check, then validation: FormRequest or $request->validate)
  → Eloquent through the school's relations, or PortalInvitationService
  → Blade view, or redirect with a flash 'status'
```

## The areas

| Area | Routes | Who | Guard |
|---|---|---|---|
| Sign-in | `/`, `/login` | guests | `guest`, `throttle:5,1` on POST |
| Invitations | `/invitations/{token}` | anyone with a token | 64-char token, `throttle`, platform admins get 404 |
| School chooser | `/dashboard`, `schools/{school}/select` | school members | `auth` |
| School workspace | `schools/{school}/…` | principal, deputy, HR, coach, student | `EnsureSchoolAccess` + role check in the controller |
| Family | `/family/…` | guardians | `User::usesFamilyPortal()` in `FamilyController` |
| Internal | `/internal/…` | the platform admin | `EnsurePlatformAdmin` |

## School isolation

A school ID in the URL is never authorization. Isolation is enforced in three layers, and a new school route needs all three:

1. **`EnsureSchoolAccess`** (`app/Http/Middleware/EnsureSchoolAccess.php`) runs on every `schools/{school}` route. It returns **404** (not 403, so another school's existence isn't confirmed) when the user is the platform admin, has no membership at that school with a role in `User::SchoolRoles`, or holds a single-school role (`User::SingleSchoolRoles`) at a *different* school. Where an account has conflicting single-school memberships, the lowest school ID wins. It stores the membership role in `$request->attributes['school_role']`.
2. **Role check in the controller or FormRequest**, reading `school_role` against a role constant on `User`: for example `StaffController::authorizeManagement()` and `StaffRequest::authorize()` (403 on failure). `SchoolOnboardingController::authorizeRole()` re-queries the membership itself, and repeats it inside the transaction.
3. **Queries through the school**: records are always loaded through the route's school, e.g. `$school->staffMembers()->findOrFail($staff)`, `$school->students()->whereKey(...)`. Never `StaffMember::find($id)`. A record from another school then 404s even if the ID is guessed.

The family side has no `{school}` in the URL: every child path goes through `$request->user()->children()`, which only returns verified, unrevoked guardian links (`guardian_student.revoked_at` is null).

## Roles

Membership is the `school_user` pivot with a `role` column. The role groups are constants on `App\Models\User`. Use them; never write a role list inline.

| Constant | Roles | Used for |
|---|---|---|
| `SchoolRoles` | principal, deputy, hr, coach, student | may enter a school workspace at all |
| `SingleSchoolRoles` | principal, deputy, hr, student | limited to one school |
| `LeadershipRoles` | principal, deputy | reviewing child requests, student invitations, announcements |
| `ManagementRoles` | principal, deputy, hr | managing staff, parent invitations |
| `StaffRoles` | principal, deputy, hr | viewing staff |
| `FamilySpaceRestrictedRoles` | all school roles | may not use the family portal |

`guardian` is also a pivot role but is not in `SchoolRoles`: guardians reach a school's data only through verified child links. A coach may hold memberships at several schools; each grants nothing at the others.

The **platform admin** is not a role: it is the account whose email exactly matches `PLATFORM_ADMIN_EMAIL` (`config('app.platform_admin_email')`, `User::isPlatformAdmin()`). It is shut out of school, family and invitation routes even if it has memberships.

## Data

| Table | Model | Notes |
|---|---|---|
| `schools` | `School` | the tenant; public contact fields only |
| `school_user` | pivot | `role` per membership |
| `staff_members` | `StaffMember` | staff records (not logins), unique email per school |
| `students` | `Student` | belongs to one school; `user_id` set only once a student activates a login |
| `guardian_student` | pivot | `verified_by`, `verified_at`, `revoked_at` |
| `child_registrations` | `ChildRegistration` | a guardian's request; `pending` until leadership reviews it |
| `portal_invitations` | `PortalInvitation` | stores a SHA-256 of the token, never the token; 7-day expiry, single use |
| `onboarding_audits` | `OnboardingAudit` | audit trail for requests, reviews, invitations, revocations |
| `platform_activities`, `platform_announcements`, `platform_backup_reports` | none | internal tools, read and written with `DB::table` |

## Invitations

`App\PortalInvitationService` is the one place invitations are issued. `canIssue()` decides who may invite whom (guardian invites: management roles; student invites: leadership, or a verified guardian of that child). `issue()` runs in a transaction that locks the school, re-checks `canIssue()`, revokes earlier open invitations for the same target, stores the token hash, writes an `OnboardingAudit` row, and sends the email after commit. `InvitationController::accept()` re-checks the issuer's *current* access before granting membership, so revoking the issuer's access also kills their pending invitations.

## Patterns to copy

- Multi-step writes that check permissions run in `DB::transaction`, lock the rows they depend on (`lockForUpdate()`), and re-check authorization inside the transaction.
- State changes that matter to a school (requests, reviews, invitations, revocations) write an `OnboardingAudit` row in the same transaction; platform actions write a `platform_activities` row.
- Mail is sent with `DB::afterCommit`, so a rolled-back transaction sends nothing.
- Unfinished features show honest empty states (`components/workspace-empty`), never fake data.

## Inventory

| Path | Holds |
|---|---|
| `app/Http/Controllers` | one controller per area: `Auth`, `School` (chooser), `Workspace` (role dashboards and sections), `Staff`, `SchoolOnboarding`, `Family`, `Invitation`, `InternalSchool`, `Platform` |
| `app/Http/Middleware` | `EnsureSchoolAccess`, `EnsurePlatformAdmin` |
| `app/Http/Requests` | `StaffRequest` (the only FormRequest so far) |
| `app/Models` | `User`, `School`, `StaffMember`, `Student`, `ChildRegistration`, `PortalInvitation`, `OnboardingAudit` |
| `app/PortalInvitationService.php` | invitation rules and issuing |
| `app/Notifications` | `PortalInvitationNotification` |
| `app/Console/Commands` | `prototype:import`, `showcase:password`, `platform:record-backup` |
| `resources/views/layouts` | `app`, `school`, `student`, `guardian` |
| `resources/views/schools` | per-role dashboards (`leadership`, `hr`, `coach`) and sections (`accounts`, `staff`, `students`, `training`, `progress`) |
| `resources/views/student`, `guardian`, `internal`, `auth` | the other areas |
| `resources/views/components` | shared `workspace-dashboard`, `workspace-tile`, `workspace-empty` |
| `database/seeders` | default seeding is empty; `ShowcaseSeeder` only on request or with `SHOWCASE_ENABLED=true` |
| `tests/Feature` | one file per area; isolation and revocation tests live here |
