# Sports app template

This orphan branch holds no app. It is the structure every new demo in this repository starts from, plus the script that stamps it out. A demo made from it is a **fresh Laravel app on its own orphan branch**: it shares the working structure with the other demos, and no code, history or product facts.

## What a new demo gets

| From | What |
|---|---|
| `composer create-project laravel/laravel:^13` | A fresh Laravel 13 app |
| `laravel/boost:^2.10` | Boost, as on `my-sports-app-demo-1` |
| `skeleton/` | `CLAUDE.md` and `AGENTS.md` working rules; `CONTRIBUTING.md`; `README.md`; `.github/` PR template and CI pinned to the new branch; `docs/INDEX.md`, `docs/wiki/` (page template + glossary), `ARCHITECTURE.md` and `laravel-standards.md` skeletons, `PRODUCT_PLAN.md`, `ROADMAP.md`, `SETUP.md`, `decisions.md`, `Ask.md`, `docs/plans/README.md`; `.gitignore` additions (local plans, `.claude/skills/`) |

Placeholders filled in by the script: `{{APP_NAME}}`, `{{BRANCH}}`, `{{DATE}}`. It stops with an error if any is left unfilled.

## Make a new demo

Needs PHP 8.2+, Composer, Node/npm and Git 2.42+ (for `worktree add --orphan`). From the root of your normal clone of this repository:

```bash
git fetch origin template
git worktree add ../sports-app-template origin/template          # once; later: git -C ../sports-app-template pull
php ../sports-app-template/new-demo.php my-sports-app-demo-2 "App name" --dry-run
php ../sports-app-template/new-demo.php my-sports-app-demo-2 "App name"
```

The dry run prints every command and file it would write, and changes nothing. The real run creates `../<repo>-my-sports-app-demo-2` as a new worktree on the orphan branch and makes the first commit there. It never pushes and never touches the checkout you run it from. Then follow the next steps it prints: `.env`, `php artisan boost:install` (check the project section at the top of `CLAUDE.md` and `AGENTS.md` survived it), tests, and push when ready.

`--path=<dir>` puts the worktree somewhere else.

## Changing the template

The template is the source of the shared structure. An improvement to agent rules, the docs layout, the PR template or CI that should reach future demos goes here, by PR into `template`, with the same review as app code. Existing demos don't change automatically: port the improvement to each one by its own PR if it should have it too.

Keep `skeleton/` free of any one demo's product facts (names, roles, domain rules). Those belong in that demo's `docs/wiki/`.

## Status

`new-demo.php` was written on a machine without PHP and has **not been run yet**. The first time, use `--dry-run`, then make a real demo with a throwaway branch name (e.g. `template-test`) and delete it afterwards (`git worktree remove ../<repo>-template-test && git branch -D template-test`).
