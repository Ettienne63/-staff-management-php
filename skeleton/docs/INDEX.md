# Docs index

**Start here.** Find the task below and open only the page it names, at the section it names. Don't read every doc: each one is small and covers one topic, so one or two pages answer most tasks.

## By task

Add a row for each wiki page as it's written (e.g. "Know what a role may see or do → wiki/access").

| You need to… | Open |
|---|---|
| Add or change a protected route | [ARCHITECTURE → Authorization](ARCHITECTURE.md#authorization), then [standards → Authorization](laravel-standards.md#authorization) |
| Find which file does what | [ARCHITECTURE → Inventory](ARCHITECTURE.md#inventory) |
| Know what a term means | [wiki/glossary](wiki/glossary.md) |
| Write code that fits | [laravel-standards](laravel-standards.md) |
| Write or review tests | [standards → Tests](laravel-standards.md#tests) |
| Run the app locally | [SETUP](SETUP.md) |
| Branch, commit, open a PR | [CONTRIBUTING](../CONTRIBUTING.md), PR template in `.github/` |
| Know what's planned next | [ROADMAP](ROADMAP.md), [PRODUCT_PLAN → Decisions needed next](PRODUCT_PLAN.md#decisions-needed-next) |
| Check whether something was already decided | [decisions](decisions.md), then the topic's wiki page |
| See what's still undecided | [Ask](Ask.md), and each wiki page's *Open or proposed* section |
| Write a new wiki page | copy [wiki/_page-template.md](wiki/_page-template.md) |

## All docs

| File | Holds |
|---|---|
| [wiki/glossary.md](wiki/glossary.md) | Terms → meaning → code |
| [wiki/_page-template.md](wiki/_page-template.md) | The format every wiki page follows |
| [ARCHITECTURE.md](ARCHITECTURE.md) | Request flow, authorization, data, inventory |
| [laravel-standards.md](laravel-standards.md) | Conventions the code follows |
| [PRODUCT_PLAN.md](PRODUCT_PLAN.md) | Vision, proposals, open product decisions (nothing confirmed) |
| [ROADMAP.md](ROADMAP.md) | Phases and delivery history |
| [SETUP.md](SETUP.md) | Local setup and verification |
| [decisions.md](decisions.md) | Dated decision log |
| [Ask.md](Ask.md) | Open questions |
| [plans/](plans/README.md) | Local `/ticket` plans (Git-ignored) |

## Keeping it true

- A change that alters a confirmed rule updates its wiki page **in the same commit**, and bumps the page's *Last checked* line.
- A proposal that gets confirmed moves from `PRODUCT_PLAN.md` to its wiki page, and the decision is logged in `decisions.md`.
- A new doc or wiki page gets a row in both tables above. Keep pages under about 5k characters; split by topic when one grows past that.
- A fact lives in one place. Link to it instead of repeating it.
