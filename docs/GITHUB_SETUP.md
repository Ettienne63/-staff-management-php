# GitHub setup

Repository: https://github.com/Ettienne63/-staff-management-php

## Verified remote state
- GitHub CLI authenticated as Ettienne63 with repository ADMIN access.
- Remote staging exists. Planning PR #1 was squash-merged into staging.
- main remains the default release branch and has not received the foundation changes.
- main and staging require pull requests, resolved conversations, and prohibit force pushes/deletion. These rules include administrators.
- Independent approval count is zero for the current solo workflow; PRs and checks still apply.
- Repository permits squash and merge commits, and disables rebase merging.
- Git identity is set locally from the verified GitHub profile, using the GitHub noreply address.
- Foundation PR #2 has been squash-merged into staging. Current migration branch: feat/prototype-laravel-migration; its PR targets staging.

## CI and promotion
.github/workflows/laravel.yml defines Branch policy and Laravel checks.
The branch policy requires main PRs to originate from this repository's staging branch.
Laravel checks install locked dependencies, validate Composer/platform requirements, run Pint/tests, and build assets.
Branch policy and Laravel checks passed their first GitHub run and are now required for both main and staging, with up-to-date branches required.
Task branches are created from staging; main promotion uses a separate release PR.
A Git branch is not a deployed staging environment. Hosting remains undecided.

## Projects board
Board creation is pending. The authenticated token lacks project scopes; gh project list failed with missing read:project.
To grant read/write Projects access from a terminal:
```powershell
gh auth refresh -s project
```
Proposed columns and milestones remain in docs/ROADMAP.md. No board has been created.

## Local edits
The pre-existing .gitignore rules are preserved and merged with Laravel exclusions during root consolidation.
The root .gitignore excludes local .env, SQLite data, dependencies, and built assets; Laravel database configuration is committed.

Reference: https://docs.github.com/en/repositories/configuring-branches-and-merges-in-your-repository/managing-protected-branches/about-protected-branches
