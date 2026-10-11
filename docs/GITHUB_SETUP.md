# GitHub setup

Repository: https://github.com/Ettienne63/-staff-management-php

## Public repository snapshot

The public repository page showed `main` as the default branch and no open pull requests when checked on 2026-10-10. The local `feat/sports-dashboard-redesign` branch had not been pushed at that time.

The GitHub CLI (`gh`) is unavailable in the current development environment. Branch protection settings, required checks, project board state, and CLI authentication therefore could not be re-verified. Treat the historical setup notes below as unconfirmed until checked in GitHub.

## Previously recorded setup

- The repository was configured with `main` as the release branch and `staging` as the integration branch.
- Planning PR #1 and foundation PR #2 were recorded as squash-merged into `staging`. The earlier migration branch `feat/prototype-laravel-migration` is historical; it is not the current local branch.
- Branch protection was recorded as requiring pull requests, resolved conversations, and prohibiting force pushes and deletion on `main` and `staging`. Verify these rules in repository settings before relying on them.
- The solo workflow was recorded with no independent approval requirement. Pull requests and CI checks were still expected.
- Squash and merge commits were enabled, while rebase merging was disabled.
- The local Git identity was recorded as using the verified GitHub profile and its noreply address.

## CI and promotion

On 2026-10-11, the user selected `my-sports-app-demo-1` as the active demo integration branch and approved consolidating all existing branch histories there. The planning and foundation branch trees exactly matched their earlier squash merges; their original histories were joined while preserving the updated application files. `main` remains at the basic PHP prototype checkpoint `8783e0c`; `staging` retains its previous integration checkpoint.

`.github/workflows/laravel.yml` runs branch policy and Laravel checks for pull requests targeting `my-sports-app-demo-1`, `staging`, or `main`, and for pushes to those branches. The historical gate requiring main release PRs to originate from this repository's staging branch remains; it does not authorize a release. The workflow validates Composer, checks platform requirements, runs Pint and PHPUnit, and builds frontend assets.

Create task branches from an up-to-date `my-sports-app-demo-1` branch and target it with task pull requests. There is no automatic promotion to `main`; changes to the preserved prototype require a separate explicit request. A Git branch is not a deployed environment. See `docs/HOSTED_UPDATES.md` for hosted demo updates.

The consolidation does not change GitHub's default branch, protection settings, existing PR records, or deployed checkout. Those settings require separate verification before claiming they were changed.

The remote requirement and success status of CI checks were recorded during earlier setup but could not be re-verified here.

## Projects board

A previous CLI attempt to list projects failed because its token lacked the `read:project` scope. Current project board status is unverified. To refresh project access from a terminal when `gh` is available:

```powershell
gh auth refresh -s project
```

Proposed columns and milestones are in `docs/ROADMAP.md`.

## Repository notes

The pre-existing `.gitignore` rules were preserved and merged with Laravel exclusions during root consolidation. The root `.gitignore` excludes local `.env`, SQLite data, dependencies, and built assets; Laravel database configuration is committed.

Reference: https://docs.github.com/en/repositories/configuring-branches-and-merges-in-your-repository/managing-protected-branches/about-protected-branches
