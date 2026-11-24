# My Sports App Demo 2

Laravel app started 2026-11-24 from the sports app template. What it is for: see `docs/PRODUCT_PLAN.md` (fill it in first).

Codex reads `AGENTS.md`; Claude reads this file. Product facts live in the docs, not in either agent file, so they stay in one place.

## Context: index first

Open `docs/INDEX.md` first, find the task, and read only the page it names. Don't read every doc, and don't open `PRODUCT_PLAN.md` for a confirmed rule: the rules are in `docs/wiki/<topic>.md`. Briefs for subagents name the one or two pages they need, not the whole `docs/` folder.

## Git and branches

- `my-sports-app-demo-2` is this app's integration branch. Branch from an up-to-date copy of it and target it with PRs. Other demos in this repository are separate apps on their own branches: never merge between them.
- One scope per branch: `feat/`, `fix/`, `docs/` or `chore/` plus a short kebab-case name (see `CONTRIBUTING.md`).
- Commits: `type(scope): imperative summary`. Stage explicit files, never `git add -A`. Keep `.env`, SQLite files, `vendor/`, `node_modules/` and built assets out of Git.
- You may create task branches and commit on your own. **Ask before every push, and before opening or updating a PR.** Never merge, never force-push a shared branch.
- `gh pr create` does not load `.github/pull_request_template.md`: read it and fill in every section yourself.

## Rules that matter most here

- Every data path checks authorization server-side. An ID in the URL or session is not authorization (see `docs/ARCHITECTURE.md` → Authorization once it is written). Each protected route gets a feature test showing an unauthorized user is refused.
- No real names, customers or personal data anywhere: tests use factories, demos use a dedicated seeder.
- Run `vendor/bin/pint --dirty --format agent` on changed PHP and the narrowest `php artisan test --compact` that covers the change before committing. If PHP isn't available on the machine, say so instead of claiming the checks passed.
- A change to a confirmed rule updates its `docs/wiki/` page in the same commit (and its *Last checked* line). A decision is appended to `docs/decisions.md`; a confirmed proposal moves from `PRODUCT_PLAN.md` to its wiki page. See *Keeping it true* in `docs/INDEX.md`.

## Plans stay local

`/ticket` writes a readable plan per task to `docs/plans/`. Those files are ignored by Git on every machine (only `docs/plans/README.md` is committed): never commit or force-add a plan.

