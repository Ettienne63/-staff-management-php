# Project instructions

## Product requirements
Start at docs/INDEX.md and open only the page the task needs. Confirmed product rules live in docs/wiki/, one page per topic, the single source both Codex and Claude read; docs/PRODUCT_PLAN.md holds only the vision, proposals and open decisions. When a rule changes, update its wiki page in the same commit, not this file, and append the decision to docs/decisions.md. See docs/ARCHITECTURE.md for where rules are enforced in code and docs/laravel-standards.md for the code conventions.

## Implementation and runtime
One Laravel 13 app at the repository root, started 2026-11-24 from the sports app template on its own orphan branch. Use PHP 8.5 and Composer; Node/npm compile assets. Serve public/ only.
See docs/SETUP.md for paths and actual commands.

## Workflow and docs
`my-sports-app-demo-2` is this app's integration branch. Create task branches from it and target it with task PRs. Other demos in this repository are unrelated apps on their own branches; never merge between them.
No direct pushes to protected branches. Use type(scope): imperative summary.
Preserve user changes; stage explicit files. Commit focused, useful increments.
Keep each branch focused on one feature, bug fix, documentation task, or maintenance task. Use feat/, fix/, docs/, or chore/ names matching the work. If a task depends on unmerged work, record that dependency instead of silently mixing scopes. When checks pass and a PR is ready for my-sports-app-demo-2, notify the user and wait for explicit merge approval; permission to commit or push a task branch does not authorize merging.
Commit completed, reviewable increments after the applicable checks pass. Use fix for correcting existing behavior, feat for new capabilities, docs for documentation, refactor for behavior-preserving restructuring, test for coverage, and chore/build/ci for the corresponding maintenance or tooling work.
Update the docs/wiki/ page (confirmed rules), docs/PRODUCT_PLAN.md (proposals) or docs/ROADMAP.md when decisions change. Mark proposals clearly.
README.md explains the canonical structure; CONTRIBUTING.md defines collaboration rules.
No example names are actual customer records.

## Code comments
Do not add routine comments when implementing features; prefer clear names and straightforward code.
Add a brief comment only when sensitive behavior, a non-obvious constraint, or a maintenance caveat needs explanation.
Explain why the detail matters or what must be preserved when changing it; do not narrate what the code already says.
Keep necessary PHPDoc type annotations concise and avoid redundant documentation blocks.

## Data and validation
Keep credentials, real personal data, .env, SQLite files, dependencies, and built assets out of Git.
config/database.php is Laravel configuration and is committed; secrets live in .env.
Check authorization server-side on every data path. An ID supplied by the client is not authorization.
Use password hashing, expiring single-use tokens, CSRF protection, validation, and audited corrections.
Run meaningful feature tests for each protected path, and also run Pint, Composer checks, and builds.

