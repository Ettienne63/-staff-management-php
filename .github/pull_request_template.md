<!-- Title: `type(scope): imperative summary`, matching the branch's scope. `gh pr create` does not load this template: fill in every section yourself. -->

## Problem and outcome

Describe what changes and why, as a bullet list of what this PR does.

## How to test

<!-- Steps for a reviewer who has not seen the code: the seeder to run and any `.env` values, which user or role to sign in as, the URL to open, what to do, and the expected result. Include one check that an unauthorized user is refused. -->

1.

## Validation

List checks performed and actual results (commands and counts). For planning, identify decisions versus proposals.

## Access and data impact

Describe authorization, personal data, migrations, and any relevant regression checks.

## Screenshots

<!-- Before/after for any visible change. Delete this section for non-UI changes. -->

## Deployment notes

<!-- Anything beyond merge-and-go: new migration, new or changed `.env` variable, a seeder or Artisan command to run, `npm run build` needed. Write "None" if there's nothing. -->

## Review checklist

Tick only what is true; delete a line that does not apply and say why.

**Branch**
- [ ] Task PR targets my-sports-app-demo-2, never another demo's branch.
- [ ] Changes belong to one task and use the documented naming convention.

**Checks**
- [ ] `vendor/bin/pint --test`, `php artisan test` and `npm run build` pass locally.
- [ ] New behaviour has feature tests, including an unauthorized user being refused on new protected routes.

**Data and access**
- [ ] No secrets, real personal data, or unrelated local files included.
- [ ] Every new data path checks authorization server-side.

**Docs and review**
- [ ] Every changed rule is updated on its `docs/wiki/` page in this PR (with *Last checked*), decisions are logged in `docs/decisions.md`, `docs/ARCHITECTURE.md` still matches the code, and unresolved decisions are explicit.
- [ ] A separate reviewing agent reviewed the diff, its findings were fixed or consciously left, and I reviewed the diff myself.
