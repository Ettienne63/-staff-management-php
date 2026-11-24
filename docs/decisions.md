# Decisions log

Append-only, newest at the bottom: one line per decision, `date · topic · question → answer`. Decisions before 2026-11-24 are reflected in the `docs/wiki/` pages (moved word for word from the dated sections of `docs/PRODUCT_PLAN.md`). The wiki holds the current state, this file the history. Open questions live in `docs/Ask.md` until answered, then move here.

- 2026-11-24 · Agent setup · Where should Claude get the project rules, given Claude Code reads CLAUDE.md and the rules lived in AGENTS.md? → Separate CLAUDE.md working rules for Claude; AGENTS.md stays Codex's.
- 2026-11-24 · Agent setup · How to stop the two agent files drifting on product facts? → Product facts live only in docs/PRODUCT_PLAN.md; both agent files point to it.
- 2026-11-24 · Agent setup · How much may Claude do without asking? → Create task branches and commit freely; ask before every push and PR; never merge.
- 2026-11-24 · Docs · Which structure docs to add? → docs/ARCHITECTURE.md, docs/laravel-standards.md, this log, and docs/plans/.
- 2026-11-24 · Docs · How do decisions.md and PRODUCT_PLAN.md split? → This file is the dated log; the current confirmed state lives in docs/wiki/ (superseded the same day, see below).
- 2026-11-24 · Docs · Commit task plans in docs/plans/? → No: plans stay local on every machine; only docs/plans/README.md is committed.
- 2026-11-24 · Tooling · Commit the project's Claude skills (commit, pr-review)? → No: they live in a separate private skills repo and are linked into .claude/skills/, which Git ignores.
- 2026-11-24 · Tooling · Add a pre-commit hook for Pint? → Not for now: it needs a new dependency, and CI already runs Pint.
- 2026-11-24 · Workflow · Base for the agent-setup branch? → my-sports-app-demo-1 (staging holds only the PHP prototype since PR #9).
- 2026-11-24 · Docs · Where should a wiki live? → In the repo under docs/wiki/, not the GitHub Wiki, so it ships with every clone and is reviewed in PRs.
- 2026-11-24 · Docs · What happens to PRODUCT_PLAN.md once topic pages exist? → Confirmed rules move word for word into docs/wiki/<topic>.md; PRODUCT_PLAN.md keeps the vision, proposals and open decisions.
- 2026-11-24 · Docs · How do agents use the docs and keep them current? → Read docs/INDEX.md first and open only the page a task needs; a change to a rule updates its page in the same commit.
- 2026-11-24 · Workflow · How should a future demo (e.g. my-sports-app-demo-2) start? → A fresh Laravel app with a different product direction, on an orphan branch in this repo, stamped from an orphan `template` branch by `new-demo.php`; it shares structure only.
