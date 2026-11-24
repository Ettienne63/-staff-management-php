# Laravel standards

The conventions this codebase already follows. Each rule here is read from the existing code, so new code should look like its neighbours. The generic PHP and Laravel rules (constructor promotion, return types, `php artisan make:`, Pint) are in the Laravel Boost block of `AGENTS.md` / `CLAUDE.md` and are not repeated here. When a rule here changes, change the code and this file together.

## Authorization

- **Every route under `schools/{school}` sits behind `EnsureSchoolAccess`.** Do not add school routes outside that group.
- **Check the role before anything else** in the action: `abort_unless(in_array($request->attributes->get('school_role'), User::ManagementRoles, true), 403);`, or a private `authorize…()` helper when the controller repeats it. FormRequests check the same attribute in `authorize()`.
- **Use the role constants on `User`.** Never write `['principal', 'deputy']` inline. A new permission set is a new constant.
- **Load records through the school**: `$school->staffMembers()->findOrFail($id)`, never `StaffMember::findOrFail($id)`.
- **404 for "not yours", 403 for "yours but not allowed".** Another school's record, a family path for a non-guardian, or a platform admin on a school route returns 404. A member without the right role returns 403. Spent invitations return 410.
- Platform admins are excluded explicitly (`abort_if($request->user()->isPlatformAdmin(), 404)`) on any route a membership alone would open.

## Controllers and validation

- Route parameters for child records are typed `int` and constrained with `whereNumber()`; the school is route-model-bound (`School $school`).
- Validate with a FormRequest when the rules are shared between store and update (`StaffRequest`); otherwise inline `$request->validate([...])`. Uniqueness is scoped to the school: `Rule::unique(...)->where('school_id', $school->id)`.
- User-facing failures inside business logic throw `ValidationException::withMessages([...])` with a sentence the user can act on.
- Writes end with `redirect()->route(...)->with('status', '…')`: a named route and a short sentence.
- Every POST that sends email or creates a request is throttled (`throttle:10,1`).
- A rule that spans several models (invitations) lives in a service class, not in two controllers.

## Data

- Multi-step writes: `DB::transaction`, `lockForUpdate()` on the rows the decision depends on, and authorization re-checked inside the transaction.
- Audit school-relevant state changes in the same transaction (`OnboardingAudit::create([...])`).
- Email after commit (`DB::afterCommit`).
- Tokens are stored hashed (`hash('sha256', $token)`), never in plain text.
- Emails are compared lower-cased and trimmed. Names or shared emails never link identities automatically.
- Models declare `#[Fillable([...])]` (and `#[Hidden]` where needed) and casts in a `casts()` method.

## Views

- One layout per area (`layouts/school`, `student`, `guardian`, `app`); pages extend it.
- Pages are grouped by audience folder (`schools/leadership`, `schools/hr`, `student/…`); a section shared by roles lives under `schools/<section>/index.blade.php`.
- Reuse the `components/workspace-*` components before writing new markup.
- A feature that isn't built shows `workspace-empty` with honest text, never sample data.
- Links use named routes: `route('staff.index', $school)`.

## Tests

- PHPUnit feature tests in `tests/Feature`, one file per area, `use RefreshDatabase`.
- Method names describe the rule: `test_school_members_see_only_their_school_records`.
- Data comes from factories (`School::factory()`, `StaffMember::factory()->for($school)`). Memberships come from a small helper in the test class (`member($school, 'hr')`).
- Every new school route gets: the allowed role sees it, a role without permission gets 403, a member of another school gets 404, and the platform admin gets 404. Revocation paths get a test that access stops immediately.
- No real names or emails: factories or `.test` / `.invalid` addresses.

## Comments

Follow `AGENTS.md` → Code comments: none for what the code says; a short one only for a non-obvious constraint, saying why it must be kept.
