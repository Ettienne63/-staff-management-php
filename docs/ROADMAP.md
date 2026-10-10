# Proposed roadmap

No product features are being implemented in this planning phase.
Effort estimates are relative (S/M/L), not delivery promises.
Priority rubric: value 1–5, access/data risk reduced 1–5, dependency enablement 1–5.
Priority score = sum of those three dimensions. Foundational dependencies override numerical ranking.

| Phase | Outcome                                       | Value | Risk reduced | Enables | Total | Effort |
| ----- | --------------------------------------------- | ----: | -----------: | ------: | ----: | ------ |
| 0     | Confirm product and repository conventions    |     4 |            4 |       5 |    13 | S      |
| 1     | Choose stack and establish Laravel foundation |     4 |            4 |       5 |    13 | M      |
| 2     | School identity and authorization             |     5 |            5 |       5 |    15 | L      |
| 3     | Coach/student roster and grades               |     5 |            3 |       5 |    13 | M      |
| 4     | Programs and assessment recording             |     5 |            4 |       5 |    14 | L      |
| 5     | Progress and explainable scoring              |     5 |            4 |       3 |    12 | L      |
| 6     | Pilot readiness and release                   |     5 |            5 |       3 |    13 | M      |

## Phase 0 — planning (current)

Confirm page map, role matrix, pilot sports, import format, and school permission ownership.
Establish task branches, staging, commit conventions, PR templates, and GitHub safeguards.
Done when documentation records decisions versus proposals and repository setup status is verifiable.

## Phase 1 — framework and data decisions

Choose supported PHP/Laravel versions; database engine; frontend approach; tenant isolation strategy.
Proposed direction: Laravel monolith with server-rendered pages to limit initial complexity. This remains undecided.
Compare shared tables with strict school scoping against separate databases: operational cost, migrations, backups, and isolation.
Create Laravel only after architecture approval; preserve the prototype and use a separate synthetic development database.
Introduce Composer/npm manifests and lockfiles, environment examples, test framework, formatting, and CI.
Done when a fresh checkout can install, run, test, and build using documented commands.
Depends on Phase 0.

## Phase 2 — tenancy and access

Implement school memberships, school selection, authorization, and separation of internal/client areas.
Test cross-school direct URLs, search, exports, files, and jobs, plus permission revocation.
Done when authorized multi-school viewing works and unauthorized access consistently fails.
Depends on Phase 1; must precede student data features.

## Phase 3 — roster

Implement coach/student relationships, grades 1–12 with academic year, and student profiles.
If CSV is approved: preview imports, validate duplicates and grade values, and keep rows school-scoped.
Done when a coach can manage authorized rosters without leaking other school records.
Depends on Phase 2.

## Phase 4 — programs and results

Implement approved assessment definitions, units/protocols, baseline and repeated results, program assignments, and completion.
Include invalid-input handling and audited corrections.
Done when a complete baseline → training → reassessment cycle can be recorded.
Depends on Phase 3.

## Phase 5 — progress and scoring

Show raw measurements, comparable progress charts, and separate score components.
Introduce a combined score only when normalization and weights are agreed and versioned.
Done when the example 4.8 → 4.5 second result shows 6.25% improvement and invalid/noncomparable baselines are handled explicitly.
Depends on Phase 4.

## Phase 6 — pilot and release

Run a pilot with synthetic data before permitted real student data.
Check accessibility, access roles, backup/restore, export rules, support process, and privacy decisions.
Configure a separate staging deployment/database when hosting is chosen.
Done when the agreed acceptance checklist passes on staging and a release PR is reviewed.

## Tradeoffs and deferred work

Prioritize secure recording and personal progress over rankings, native mobile apps, integrations, and automated recommendations.
Delay complex scoring until reliable assessment data exists.
Avoid Redis, queues, paid services, or tenancy packages until a specific requirement justifies them.
First suggested implementation ticket after approval: framework foundation, not the scoring engine.

## Proposed task-board columns

Backlog → Needs decision → Ready → In progress → In review → Staging validation → Done.
Track phase, priority, type, size, and acceptance criteria per item.
An item reaches Done after staging validation; releases are separately promoted to main.
