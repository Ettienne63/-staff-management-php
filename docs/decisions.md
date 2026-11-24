# Decisions log

Append-only, newest at the bottom: one line per decision, `date · topic · question → answer`. Earlier decisions are recorded in the dated sections of `docs/PRODUCT_PLAN.md`; that file holds the current state, this one the history. Open questions live in `docs/Ask.md` until answered, then move here.

- 2026-11-24 · Agent setup · Where should Claude get the project rules, given Claude Code reads CLAUDE.md and the rules lived in AGENTS.md? → Separate CLAUDE.md working rules for Claude; AGENTS.md stays Codex's.
- 2026-11-24 · Agent setup · How to stop the two agent files drifting on product facts? → Product facts live only in docs/PRODUCT_PLAN.md; both agent files point to it.
- 2026-11-24 · Agent setup · How much may Claude do without asking? → Create task branches and commit freely; ask before every push and PR; never merge.
- 2026-11-24 · Docs · Which structure docs to add? → docs/ARCHITECTURE.md, docs/laravel-standards.md, this log, and docs/plans/.
- 2026-11-24 · Docs · How do decisions.md and PRODUCT_PLAN.md split? → This file is the dated log; PRODUCT_PLAN.md stays the current confirmed state.
- 2026-11-24 · Docs · Commit task plans in docs/plans/? → No: plans stay local on every machine; only docs/plans/README.md is committed.
- 2026-11-24 · Tooling · Commit the project's Claude skills (commit, pr-review)? → No: they live in a separate private skills repo and are linked into .claude/skills/, which Git ignores.
- 2026-11-24 · Tooling · Add a pre-commit hook for Pint? → Not for now: it needs a new dependency, and CI already runs Pint.
- 2026-11-24 · Workflow · Base for the agent-setup branch? → my-sports-app-demo-1 (staging holds only the PHP prototype since PR #9).
