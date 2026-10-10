# GitHub repository setup

Repository: https://github.com/Ettienne63/-staff-management-php
The existing repository is retained; no rename or replacement is planned.

## Target workflow

Task branch → pull request to staging → staging validation → release PR from staging to main.
Keep main as the release/default branch. Set task PR base to staging explicitly.

## Remote setup status

Local staging and docs/sports-app-plan branches exist. Planning documents are prepared on docs/sports-app-plan. Publishing staging failed because no GitHub credentials are available. Remote branches, protection settings, labels, milestones, and a Projects board have not been configured.
GitHub CLI 2.102.0 reports no authenticated GitHub hosts. No Git author name/email is configured, so the planning files remain uncommitted. Configure your actual commit identity before committing; preserve the unrelated existing .gitignore edit.
A GitHub Projects board is a separate account-level resource; the proposed columns are in ROADMAP.md.

## Proposed repository settings

- Protect main and staging: require PRs, reject force pushes and deletion.
- Require resolved review conversations.
- For a team, require one independent approving review. For a solo maintainer, do not configure an impossible self-review requirement; agree the reviewer policy first.
- Require relevant CI checks once those checks exist; do not select nonexistent status checks.
- Prefer squash merge for task PRs; merge commits for staging-to-main release PRs.
- Restrict main PRs to staging promotions through an enforced workflow once implementation tooling is introduced.
- Keep bypass/admin exceptions narrow and explicit.

Branch protections alone do not necessarily enforce the PR source branch.
Conventions and AGENTS.md do not replace server-side rules. GitHub plan/account capabilities must be checked.

## Suggested labels and board

Labels: type:feature, type:bug, type:docs, type:tooling; priority:high/medium/low; needs-decision; tenancy; assessments.
Board columns: Backlog, Needs decision, Ready, In progress, In review, Staging validation, Done.
Use a milestone for each roadmap phase. These are proposals until remote creation is verified.

## Manual fallback if authentication is unavailable

1. Sign in using gh auth login from a normal terminal.
2. Create staging from main once using GitHub's branch selector.
3. Publish docs/sports-app-plan and open a PR targeting staging.
4. Configure rules for main and staging under repository Settings.
5. Create/link a GitHub Projects board if desired and seed it from ROADMAP.md.

Reference: https://docs.github.com/en/repositories/configuring-branches-and-merges-in-your-repository/managing-protected-branches/about-protected-branches
