# Contribution conventions

## Branch workflow

- main: preserved basic PHP prototype with login and staff CRUD.
- my-sports-app-demo-1: active demo integration branch containing the consolidated Laravel work.
- staging: previous integration checkpoint, retained for history.
- One separate branch per feature, fix, documentation task, or tooling change.
- Create task branches from an up-to-date my-sports-app-demo-1.
- PRs from task branches target my-sports-app-demo-1. Changes to main require a separate explicit request.
- Use reviewed PRs for routine integration. Never push directly to protected branches or force-push shared branches. The user explicitly approved the 2026-10-11 history consolidation into my-sports-app-demo-1.
- Git branch names do not establish deployed environments. Follow the hosted update guide for deployment work.
- Codex may choose and create task branches autonomously. Start a new branch from my-sports-app-demo-1 for a distinct independent task; continue the current task branch for related implementation and fixes. Preserve unmerged dependencies and user changes when switching branches.
- Keep one focused scope per branch. New features use feat/, bugs and regressions use fix/, documentation uses docs/, and maintenance uses chore/ or its applicable type. Do not bundle unrelated fixes, features, and documentation tasks into a growing feature branch. Documentation and tests directly needed by that task belong with its implementation.
- For dependent tasks, record the prerequisite branch/PR and integrate prerequisites first. Avoid rewriting shared branch history to organize already published work.
- When a task passes its applicable checks, report that its PR is ready for my-sports-app-demo-1 and summarize the changes and checks. Wait for the user's explicit approval before merging; autonomous branch, commit, and push decisions do not grant merge approval.

Branch names: type/short-kebab-case-description.
Examples: feat/student-import, fix/school-access-check, docs/sports-app-plan, chore/laravel-bootstrap.

Example for a new feature:

```powershell
git fetch origin
git switch my-sports-app-demo-1
git pull --ff-only origin my-sports-app-demo-1
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
Codex may choose the commit type and timing and commit completed, reviewable increments after applicable checks pass, without waiting for a separate commit request. Use `fix` for correcting existing behavior, `feat` for new capabilities, `docs` for documentation, `refactor` for restructuring without changing behavior, `test` for coverage, and `chore`, `build`, or `ci` for the corresponding maintenance or tooling changes.
Before committing, inspect git status and git diff. Stage explicit files; do not include local credentials or unrelated edits.
Commit identity must be configured by the contributor; do not invent an author's name or email.

## Pull requests

Use the same title format as commits.
Describe the problem, resulting behavior, validation, and any unresolved decisions.
Include authorization and cross-school isolation evidence for changes touching tenant data.
Keep business behavior, schema, and documentation consistent.
Prefer squash merging task PRs into my-sports-app-demo-1. Demo integration does not automatically promote work to main.
For an existing chain of published dependent task branches, merge commits may preserve prerequisite ancestry and avoid repeating already integrated commits in later PRs. Explain this exception when using it. New independent task branches should follow the normal squash policy.
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
