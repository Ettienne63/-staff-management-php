# Proposed roadmap

## Page foundation delivery (implemented locally, 2026-10-10)

1. Tailwind/Blade shared layout and accessibility foundation.
2. Restricted internal school directory and creation with initial principal assignment.
3. School leadership/HR, coach, and student page foundations with school and role checks.

Account invitations, HR account controls, principal-only vice/ownership changes, guardian linking, student records, sports editing, completion, and evidence links are future workflows. Student pages in this delivery contain no student records or results.

Local implementation is complete for these three page phases: Tailwind styling, restricted platform school creation, and role-specific client workspaces. School access is checked on each request, including after membership revocation or role changes. HR can manage the existing staff CRUD; students cannot access it.

The dashboard design iteration adds a responsive, role-aware sidebar, school and workspace tiles, and an empty schedule panel using the logo's navy, teal, and green palette. The production asset build passed locally. Browser visual review and staging validation remain outstanding.

The Laravel and school-access foundations are implemented. Invitations, student/sports features, and production tenant storage decisions remain planned.
Effort estimates are relative (S/M/L), not delivery promises.
Priority rubric: value 1–5, access/data risk reduced 1–5, dependency enablement 1–5.
Priority score = sum of those three dimensions. Foundational dependencies override numerical ranking.

| Phase | Outcome                                       | Value | Risk reduced | Enables | Total | Effort |
| ----- | --------------------------------------------- | ----: | -----------: | ------: | ----: | ------ |
| 0     | Confirm product and repository conventions    |     4 |            4 |       5 |    13 | S      |
| 1     | Establish Laravel foundation and production data strategy |     4 |            4 |       5 |    13 | M      |
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

Laravel 13, PHP 8.5, Blade, and Tailwind 4 are in use. The database engine and production tenant isolation strategy remain undecided.
Compare shared tables with strict school scoping against separate databases: operational cost, migrations, backups, and isolation.
The Laravel application, Composer/npm manifests and lockfiles, test framework, formatting, and CI are established. Continue preserving the prototype and keep local development data isolated.
Done when a fresh checkout can install, run, test, and build using documented commands.
Depends on Phase 0.

## Phase 2 — tenancy and access

School selection, internal/client separation, and per-request membership checks are implemented for current school and staff paths. Decide production tenant storage, then add invitations and defined guardian/student access flows before storing student data.
Extend cross-school authorization coverage to each new search, export, file, job, and student/sports path; preserve permission revocation behavior.
Done when the production tenant model and required invitation/access flows are implemented and unauthorized access consistently fails.
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

## Foundation progress (2026-10-10)

PHP 8.5 and Laravel 13 installed alongside the prototype. SQLite setup is isolated from MariaDB.
Lockfiles, starter tests, asset tooling, and CI are introduced. Phase 1 remains incomplete until architecture decisions and staging validation are complete.

## Prototype incorporation (2026-10-10)

Laravel is consolidated into the repository root. Login, logout, dashboard school selection, and staff CRUD are migrated with validated CSRF-protected forms and school authorization. The local importer preserves password hashes and leaves MariaDB unchanged. Guardian accounts are confirmed requirements, but their linking/invitation flows, students, and training/scoring remain future phases. The local school membership baseline is not a final production tenancy storage decision.
