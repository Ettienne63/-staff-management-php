# Updating the alwaysdata demo

Use this guide for updates to the existing hosted demo. Run Windows commands on your computer and Linux commands in the alwaysdata SSH terminal. Run deployment commands one at a time and stop if any fails.

## For future hosted-update requests

When the user asks to update the hosted server or says their changes are missing, read this guide before proposing commands:

1. Inspect the local working tree, branch, commits, and upstream to identify the changes the user means and whether they are pushed. Do not assume the currently checked-out documentation branch is the application branch to deploy.
2. Check the actual hosted branch and commit through SSH. A local Git result does not establish what the server is running. If no authenticated server connection is available, have the user run the SSH inspection commands below and share their output.
3. Compare the hosted commit with the intended pushed commit. If they are on different branches, explicitly fetch and switch to the intended branch; pulling the old branch cannot bring in the fixes.
4. Complete the dependency install, asset build, migrations, and cache refresh as applicable. Preserve server-only configuration, data, and credentials.
5. Verify the hosted Git commit and the changed screen before reporting the site as updated. Record the confirmed result here when available; an instruction to deploy is not evidence that deployment succeeded.

### What happened on 2026-10-11

- The user ran `cd ~/apps/my-sports-app` in Windows PowerShell and received a missing-path error for `C:\Users\ettie\apps\my-sports-app`. The Git output afterward described their local checkout, not alwaysdata.
- After connecting to SSH, the user confirmed the server was still on `feat/platform-admin-tools` at `7f75548` (`feat(demo): add a private showcase password prompt`). The hosted working tree was clean.
- The intended student/family invitation fixes were pushed on `fix/student-family-invitations` at `11f2962` (`fix(onboarding): isolate student access and school invitations`). We provided explicit branch-switch and deployment commands.
- Deployment of `11f2962` has not yet been confirmed by server output or a check of the changed screen. Do not label it as the current hosted version until verified. These commits are historical reference points; inspect fresh branch tips for future changes.

The guide is maintained on `docs/alwaysdata-updates`, which contains the above application fixes plus documentation. Keep documentation changes separate from selecting the application version for the hosted demo.

## 1. Publish the intended changes

Commit and push the task branch containing your fixes from your local computer. Never include `.env`, database files, passwords, dependencies, or built assets in the commit.

The hosted demo originally used `feat/platform-admin-tools`. The student/family invitation fixes are on `fix/student-family-invitations`. Pulling one branch does not deploy changes from another branch.

For the normal project release workflow, task PRs target `staging` and approved releases promote `staging` to `main`. Deploy a task branch to this demo only when deliberately previewing that work. Do not treat a preview deployment as a reviewed release.

From 2026-10-11, keep future features, bug fixes, documentation tasks, and maintenance on separate focused branches. Notify the user when a checked PR is ready for staging and obtain explicit approval before merging. The user approved consolidating the existing completed branch chain into staging; that approval does not apply to future merges or main releases. After consolidation, use staging for the integrated hosted demo if the user deploys that branch. Merging on GitHub does not automatically update alwaysdata; still verify and update its SSH checkout.

## 2. Connect and inspect the hosted copy

From Windows PowerShell:

```powershell
ssh my-sports-app@ssh-my-sports-app.alwaysdata.net
```

Wait until the terminal prompt starts with `my-sports-app@ssh2:` (or another alwaysdata SSH hostname). A `PS C:\...>` prompt is still Windows PowerShell. Only then run:

```bash
cd ~/apps/my-sports-app
git status --short
git branch --show-current
git log -1 --oneline
php -v
```

If the SSH connection has closed, connect again before using `~/apps/my-sports-app`. The corresponding local project directory is `C:\xampp\htdocs\my-sports-app`; the two checkouts are independent.

PHP must be 8.5. If necessary, select 8.5 under alwaysdata **Environment → PHP**, then reconnect.

If `git status --short` lists changes, inspect and preserve them before pulling. Do not run `git reset --hard`, `git clean`, or discard hosted edits to make an update pass. `.env` is ignored and normally does not appear here.

Record the current branch and commit so you know which version was running. Before database migrations, save a database backup and a private copy of the hosted `.env`. Keep both outside the served directory and out of Git.

## 3. Select the branch to deploy

If updating the branch already deployed:

```bash
git pull --ff-only
```

If deliberately deploying another pushed branch, fetch and switch first. For the student/family invitation fixes:

```bash
git fetch origin
git switch fix/student-family-invitations
git pull --ff-only
```

Replace the branch name with the intended branch for future updates. If Git reports local changes, a conflict, or diverged history, stop and resolve it; do not force the pull.

Confirm the result:

```bash
git branch --show-current
git log -1 --oneline
```

## 4. Install, build, and migrate

For an update that changes dependencies, assets, or database structure, use maintenance mode to keep visitors out during the remaining steps:

```bash
php artisan down --retry=60
composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction
npm ci --ignore-scripts
npm run build
php artisan config:clear
php artisan migrate --force --no-interaction
php artisan optimize:clear
php artisan config:cache
php artisan view:cache
php artisan up
```

Stop on the first error. If maintenance mode was enabled, the site stays unavailable until you repair the issue and run `php artisan up`. Do not bring an incomplete update online just to hide a failed deployment.

Use `composer install`, not `composer update`, to install the committed dependency versions. No background `artisan serve` or Vite development server is needed; alwaysdata serves the built application through PHP.

## 5. Check the website

Open **https://my-sports-app.alwaysdata.net** and refresh with **Ctrl+F5**. Confirm login, the changed page, and the intended role restrictions. Existing demo passwords should still work.

In the same SSH session, run `git branch --show-current` and `git log -1 --oneline` again and compare them with the intended deployed branch and commit. If the commit matches but a change is still missing, identify the exact page, login role, and behavior, confirm alwaysdata serves this checkout's `public/` directory, and inspect build/cache or application errors. Do not assume every missing change is browser caching.

The alwaysdata site must use PHP 8.5 and this document root:

```text
/home/my-sports-app/apps/my-sports-app/public
```

Keep HTTPS enabled, `APP_ENV=production`, and `APP_DEBUG=false`.

## What to preserve

- Keep the hosted `.env`; do not replace it with `.env.example` during updates.
- Keep the existing `APP_KEY`; do not run `key:generate` again for a routine update.
- Keep the database and uploaded files. Never use `migrate:fresh`, `migrate:refresh`, or destructive database resets to deploy.
- Do not reseed or change passwords during ordinary updates. Showcase setup is a separate action described in [SETUP.md](SETUP.md#local-showcase-data).
- Keep the project-root `.htaccess` denial and the `public/.htaccess` allowance. Serve only `public/`.

## If an update fails

Save the failing command and error. For an HTTP error, inspect the alwaysdata site logs and `storage/logs/laravel.log` privately; remove credentials, invitation tokens, and personal information before sharing excerpts.

For a Forbidden response, check the document root and the `Require all granted` line in `public/.htaccess`. Do not remove the source-directory protection or use `chmod -R 777`.

Code rollback and database rollback are different operations. A previous commit may not work with a newly migrated schema. Check compatibility and the backup before rolling back; do not run migration rollback commands blindly.
