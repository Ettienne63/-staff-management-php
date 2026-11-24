# Laravel standards

The house conventions, carried over from the sports app template. They're starting rules: once this app has code, keep only what it follows, add what it does differently, and change the code and this file together. The generic PHP and Laravel rules (constructor promotion, return types, `php artisan make:`, Pint) are in the Laravel Boost block of `AGENTS.md` / `CLAUDE.md` and are not repeated here.

## Authorization

- **Every protected route sits behind middleware** that decides access. Don't add a protected route outside that group.
- **Check permission before anything else** in the action, or in a FormRequest's `authorize()`. Repeated checks become one private `authorize…()` helper.
- **Name role sets as constants** (e.g. on `User`), never inline arrays of role names. A new permission set is a new constant.
- **Load records through their owner**: `$owner->items()->findOrFail($id)`, never `Item::findOrFail($id)`, so a guessed ID from someone else 404s.
- **404 for "not yours", 403 for "yours but not allowed".** Spent tokens return 410.

## Controllers and validation

- Child-record route parameters are typed `int` and constrained with `whereNumber()`; the owner is route-model-bound.
- Validate with a FormRequest when rules are shared between store and update; otherwise inline `$request->validate([...])`. Uniqueness is scoped to the owner.
- User-facing failures inside business logic throw `ValidationException::withMessages([...])` with a sentence the user can act on.
- Writes end with `redirect()->route(...)->with('status', '…')`.
- POSTs that send email or create requests are throttled.
- A rule that spans several models lives in a service class, not in two controllers.

## Data

- Multi-step writes: `DB::transaction`, `lockForUpdate()` on the rows the decision depends on, authorization re-checked inside the transaction.
- Audit access-relevant state changes in the same transaction.
- Email after commit (`DB::afterCommit`).
- Tokens are stored hashed (`hash('sha256', $token)`), never in plain text.
- Emails are compared lower-cased and trimmed; names or shared emails never link identities automatically.
- Models declare `#[Fillable([...])]` (and `#[Hidden]` where needed) and casts in a `casts()` method.

## Views

- One layout per audience; pages extend it, grouped by audience folder.
- Reuse shared components before writing new markup.
- A feature that isn't built shows an honest empty state, never sample data.
- Links use named routes.

## Tests

- PHPUnit feature tests in `tests/Feature`, one file per area, `use RefreshDatabase`.
- Method names describe the rule: `test_members_see_only_their_own_records`.
- Data comes from factories; memberships or roles from a small helper in the test class.
- Every protected route gets: the allowed user sees it, the wrong role gets 403, an outsider gets 404. Revocation paths get a test that access stops immediately.
- No real names or emails: factories, or `.test` / `.invalid` addresses.

## Comments

Follow `AGENTS.md` → Code comments.
