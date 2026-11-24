# Docs index

**Start here.** Find the task below and open only the page it names, at the section it names. Don't read every doc: each one is small and covers one topic, so one or two pages answer most tasks.

## By task

| You need to… | Open |
|---|---|
| Know what a role may see or do | [wiki/access](wiki/access.md) |
| Add or change a route under `schools/{school}` | [ARCHITECTURE → School isolation](ARCHITECTURE.md#school-isolation), then [standards → Authorization](laravel-standards.md#authorization) |
| Work on parent or student invitations, child links | [wiki/onboarding](wiki/onboarding.md), then [ARCHITECTURE → Invitations](ARCHITECTURE.md#invitations) |
| Work on `/internal` tools | [wiki/platform-admin](wiki/platform-admin.md) |
| Touch demo data or seeders | [wiki/showcase](wiki/showcase.md), [SETUP → Local showcase data](SETUP.md#local-showcase-data) |
| Change a dashboard, layout or navigation | [wiki/workspaces](wiki/workspaces.md), [standards → Views](laravel-standards.md#views) |
| Start on training, assessments or scores | [wiki/training-and-scoring](wiki/training-and-scoring.md), then the proposals it links |
| Find which file does what | [ARCHITECTURE → Inventory](ARCHITECTURE.md#inventory) |
| Know what a term means | [wiki/glossary](wiki/glossary.md) |
| Write code that fits | [laravel-standards](laravel-standards.md) |
| Write or review tests | [standards → Tests](laravel-standards.md#tests) |
| Run the app locally | [SETUP → Fresh local checkout](SETUP.md#fresh-local-checkout) |
| Update or debug the hosted demo | [HOSTED_UPDATES](HOSTED_UPDATES.md) (read all of it) |
| Branch, commit, open a PR | [CONTRIBUTING](../CONTRIBUTING.md), PR template in `.github/` |
| Know what's planned next | [ROADMAP](ROADMAP.md), [PRODUCT_PLAN → Decisions needed next](PRODUCT_PLAN.md#decisions-needed-next) |
| Check whether something was already decided | [decisions](decisions.md), then the topic's wiki page |
| See what's still undecided | [Ask](Ask.md), and each wiki page's *Open or proposed* section |

## All docs

| File | Holds | Size |
|---|---|---|
| [wiki/access.md](wiki/access.md) | Tenancy, roles, who can see what (confirmed) | 4k |
| [wiki/onboarding.md](wiki/onboarding.md) | Parent-first onboarding, guardian links, student activation (confirmed) | 4k |
| [wiki/platform-admin.md](wiki/platform-admin.md) | Internal tools and their limits (confirmed) | 2k |
| [wiki/showcase.md](wiki/showcase.md) | Demo fixtures and their rules (confirmed) | 2k |
| [wiki/workspaces.md](wiki/workspaces.md) | Dashboards and audience separation (confirmed) | 1k |
| [wiki/training-and-scoring.md](wiki/training-and-scoring.md) | What's confirmed about the unbuilt sports features | 1k |
| [wiki/glossary.md](wiki/glossary.md) | Terms → meaning → code | 2k |
| [ARCHITECTURE.md](ARCHITECTURE.md) | Request flow, isolation layers, roles, tables, inventory | 8k |
| [laravel-standards.md](laravel-standards.md) | Conventions the code follows | 4k |
| [PRODUCT_PLAN.md](PRODUCT_PLAN.md) | Vision, proposals, open product decisions (nothing confirmed) | 7k |
| [ROADMAP.md](ROADMAP.md) | Phases and delivery history | 10k |
| [SETUP.md](SETUP.md) | Local setup, prototype import, showcase data, verification | 11k |
| [HOSTED_UPDATES.md](HOSTED_UPDATES.md) | Updating the hosted demo | 9k |
| [GITHUB_SETUP.md](GITHUB_SETUP.md) | GitHub repo, CI and board state | 4k |
| [decisions.md](decisions.md) | Dated decision log | 2k |
| [Ask.md](Ask.md) | Open questions | <1k |
| [plans/](plans/README.md) | Local `/ticket` plans (Git-ignored) | — |

## Keeping it true

- A change that alters a confirmed rule updates its wiki page **in the same commit**, and bumps the page's *Last checked* line.
- A proposal that gets confirmed moves from `PRODUCT_PLAN.md` to its wiki page, and the decision is logged in `decisions.md`.
- A new doc or wiki page gets a row in both tables above. Keep pages under about 5k characters; split by topic when one grows past that.
- A fact lives in one place. Link to it instead of repeating it.
