# Contribution conventions

## Branch workflow

- {{BRANCH}}: this app's integration branch. It is an orphan branch: it shares no history with the other demos in this repository.
- One separate branch per feature, fix, documentation task, or tooling change.
- Create task branches from an up-to-date {{BRANCH}}.
- PRs from task branches target {{BRANCH}}. Never merge another demo's branch into it, or it into another demo.
- Use reviewed PRs for routine integration. Never push directly to protected branches or force-push shared branches.
- Keep one focused scope per branch. New features use feat/, bugs and regressions use fix/, documentation uses docs/, and maintenance uses chore/. Documentation and tests directly needed by a task belong with its implementation.
- For dependent tasks, record the prerequisite branch/PR and integrate prerequisites first.
- When a task passes its checks, report that its PR is ready for {{BRANCH}} and summarize the changes and checks. Wait for the user's explicit approval before merging.

Branch names: type/short-kebab-case-description.

```bash
git fetch origin
git switch {{BRANCH}}
git pull --ff-only origin {{BRANCH}}
git switch -c feat/short-description
```

## Commits

Use type(scope): imperative summary, with a bullet-list body of the actual changes, each ending in a full stop.

## Template

This app was stamped from the `template` branch. Improvements to the shared structure (agent rules, docs layout, PR template, CI) go to the `template` branch by PR as well, so the next demo starts with them.
