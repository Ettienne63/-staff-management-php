# Sports app template

This orphan branch holds no app. It is the structure every new demo in this repository starts from, plus the script that stamps it out. A demo made from it is a **fresh Laravel app on its own orphan branch**: it shares the working structure with the other demos, and no code, history or product facts.

## What a new demo gets

| From | What |
|---|---|
| `composer create-project laravel/laravel:^13` | A fresh Laravel 13 app |
| `laravel/boost:^2.10` | Boost, as on `my-sports-app-demo-1`, with its guidelines installed into both `CLAUDE.md` and `AGENTS.md` |
| `skeleton/` | `CLAUDE.md` and `AGENTS.md` working rules; `CONTRIBUTING.md`; `README.md`; `.github/` PR template and CI pinned to the new branch; a root `.htaccess` that denies access outside `public/` (for Apache hosts such as alwaysdata); `docs/INDEX.md`, `docs/wiki/` (page template + glossary), `ARCHITECTURE.md` and `laravel-standards.md` skeletons, `PRODUCT_PLAN.md`, `ROADMAP.md`, `SETUP.md`, `decisions.md`, `Ask.md`, `docs/plans/README.md`; `.gitignore` additions (local plans, `.claude/skills/`) |

Placeholders filled in by the script: `{{APP_NAME}}`, `{{BRANCH}}`, `{{DATE}}`. It stops with an error if any is left unfilled.

## Make a new demo

Needs PHP 8.3+, Composer, Node/npm and Git 2.42+ (for `worktree add --orphan`). Set `git config user.email` to your GitHub noreply address first, or the commits carry your global email. From the root of your normal clone of this repository:

```bash
git fetch origin template
git worktree add ../sports-app-template origin/template          # once; later: git -C ../sports-app-template pull
php ../sports-app-template/new-demo.php my-sports-app-demo-3 "App name" --dry-run
php ../sports-app-template/new-demo.php my-sports-app-demo-3 "App name"
```

The dry run prints every command and file it would write, and changes nothing. The real run creates `../<repo>-my-sports-app-demo-3` as a new worktree on the orphan branch and makes two commits there: the app with the skeleton, then the Boost guidelines. It stops if `boost:install` changed the project section of `CLAUDE.md`. It never pushes and never touches the checkout you run it from. Then follow the next steps it prints: tests, a check of the commit author, and push when ready.

### Without PHP on your machine

Run it in Docker, mounting the folder that holds your clone at the same path so the worktree's Git paths stay valid:

```bash
P=$(cd .. && pwd)
docker run --rm -v "$P:$P" -w "$PWD" -v ~/.gitconfig:/root/.gitconfig:ro \
  -e GIT_CONFIG_COUNT=1 -e GIT_CONFIG_KEY_0=safe.directory -e GIT_CONFIG_VALUE_0='*' \
  composer:2 sh -c 'apk add -q nodejs npm && php ../sports-app-template/new-demo.php my-sports-app-demo-3 "App name"'
```

The container sees the global `~/.gitconfig` only, so a repo-level `user.email` set in your clone still applies (it lives in the mounted `.git/config`).

`--path=<dir>` puts the worktree somewhere else.

## Changing the template

The template is the source of the shared structure. An improvement to agent rules, the docs layout, the PR template or CI that should reach future demos goes here, by PR into `template`, with the same review as app code. Existing demos don't change automatically: port the improvement to each one by its own PR if it should have it too.

Keep `skeleton/` free of any one demo's product facts (names, roles, domain rules). Those belong in that demo's `docs/wiki/`.

## Status

`new-demo.php` made `my-sports-app-demo-2` on 2026-11-24 (in Docker, `composer:2`, PHP 8.5), and its CI passed on first push. After changing the script, test it with a throwaway branch name (e.g. `template-test`) and delete it afterwards (`git worktree remove ../<repo>-template-test && git branch -D template-test`).
