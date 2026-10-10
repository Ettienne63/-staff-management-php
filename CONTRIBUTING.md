# Contribution conventions

## Branch workflow

- main: approved release history.
- staging: integration branch for reviewed work.
- One separate branch per feature, fix, documentation task, or tooling change.
- Create task branches from an up-to-date staging.
- PRs from task branches target staging. Release PRs target main from staging.
- Never push directly to main or staging. Do not force-push shared branches.
- A staging branch is not a deployed staging environment; hosting and deployments are a later decision.

Branch names: type/short-kebab-case-description.
Examples: feat/student-import, fix/school-access-check, docs/sports-app-plan, chore/laravel-bootstrap.

Example after staging exists remotely:

```powershell
git fetch origin
git switch staging
git pull --ff-only origin staging
git switch -c feat/student-import
```

## Commits

Use type(scope): imperative summary.
Types: feat, fix, docs, refactor, test, chore, build, ci.
Scopes describe an area, such as students, assessments, tenancy, planning, or tooling.
Examples:

- docs(planning): define sports app roadmap
- feat(students): add grade filters
- fix(tenancy): reject access without school membership

Keep each commit focused. Explain material tradeoffs in the body. Use BREAKING CHANGE in the body when applicable.
Before committing, inspect git status and git diff. Stage explicit files; do not include local credentials or unrelated edits.
Commit identity must be configured by the contributor; do not invent an author's name or email.

## Pull requests

Use the same title format as commits.
Describe the problem, resulting behavior, validation, and any unresolved decisions.
Include authorization and cross-school isolation evidence for changes touching tenant data.
Keep business behavior, schema, and documentation consistent.
Prefer squash merging task PRs into staging. Prefer a merge commit for staging-to-main release PRs so branches share release ancestry.
Remove merged task branches when appropriate.

## Code comments
Do not add routine comments when implementing features; prefer clear names and straightforward code.
Add a brief comment only when sensitive behavior, a non-obvious constraint, or a maintenance caveat needs explanation.
Explain why the detail matters or what must be preserved when changing it; do not narrate what the code already says.
Keep necessary PHPDoc type annotations concise and avoid redundant documentation blocks.

## Checks

Planning or documentation changes: verify links, consistency, and decision/proposal labels.
Laravel changes: use PHP 8.5 and run the narrowest relevant PHPUnit tests, `vendor/bin/pint --dirty --format agent`, `composer validate --strict`, and `composer check-platform-reqs` as applicable. Run `npm.cmd run build` when frontend assets change.
Review frontend changes at desktop and mobile widths, and confirm reduced-motion behavior when animations change.
No fabricated passing checks. CI cannot prove access control without appropriate tests.
Enforcement requires GitHub rules; this file is a contributor policy, not remote branch protection.
