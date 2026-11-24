<!-- Title: `type(scope): imperative summary`, matching the branch's scope. `gh pr create` does not load this template: fill in every section yourself. -->

## Problem and outcome

Describe what changes and why, as a bullet list of what this PR does.

## How to test

<!-- Steps for a reviewer who has not seen the code: the seeder to run (e.g. `php artisan db:seed --class=ShowcaseSeeder`) and any `.env` values, which role to sign in as, the URL to open, what to do, and the expected result. Include one check that a user of another school, or the wrong role, is refused. -->

1.

## Validation

List checks performed and actual results (commands and counts). For planning, identify decisions versus proposals.

## Access and data impact

Describe school authorization, student data, migrations, and any relevant regression checks.

## Screenshots

<!-- Before/after for any visible change. Delete this section for non-UI changes. -->

## Deployment notes

<!-- Anything beyond merge-and-go: new migration, new or changed `.env` variable, a seeder or Artisan command to run on the hosted demo (see docs/HOSTED_UPDATES.md), `npm run build` needed. Write "None" if there's nothing. -->

## Review checklist

Tick only what is true; delete a line that does not apply and say why.

**Branch**
- [ ] Task PR targets my-sports-app-demo-1; any change to main has separate explicit approval.
- [ ] Changes belong to one task and use the documented naming convention.

**Checks**
- [ ] `vendor/bin/pint --test`, `php artisan test` and `npm run build` pass locally.
- [ ] New behaviour has feature tests, including another school's user (404) and the wrong role (403) for new school routes.

**Data and access**
- [ ] No secrets, actual student data, or unrelated local files included.
- [ ] Every new school data path checks membership server-side and loads records through the school.

**Docs and review**
- [ ] Documentation is updated (`docs/PRODUCT_PLAN.md`, `docs/ARCHITECTURE.md`, `docs/decisions.md` where their claims changed) and unresolved decisions are explicit.
- [ ] A separate reviewing agent reviewed the diff, its findings were fixed or consciously left, and I reviewed the diff myself.
