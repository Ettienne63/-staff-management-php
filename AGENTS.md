# Project instructions

## Purpose and confirmed requirements

Build a school sports development app tracking training, measured performance, and improvement over time.

- Initial roles: coaches and students. Coach data entry and student access permissions still need confirmation.
- A coach can work with many students, grouped by grades 1–12.
- Each school is an isolated tenant.
- A coach may access multiple schools only through explicit permissions for each school. Cross-school viewing never means unrestricted access.
- Client-facing school/student experiences and internal platform operations must be separated. Exact internal roles are undecided.
- Scores will combine training participation, performance, and improvement; formula and weights are undecided.
- Current scope is planning, documentation, tooling, and repository workflow. Do not implement application features or migrate to Laravel until requested.

## Current code and intended stack

Current code is a plain PHP staff-management prototype, not Laravel. Laravel/PHP is the intended application stack, using Composer and Node.js/npm.
Current runtime: XAMPP PHP 8.2.12 and MariaDB; local URL http://localhost/my-sports-app/.
Working directory: C:/xampp/htdocs/my-sports-app. Verify this location before changing files.
No composer.json, package.json, artisan, or configured automated tests exist yet.
Database engine, Laravel version, tenancy implementation, and frontend stack remain proposals until confirmed.
See docs/SETUP.md for installed tools and compatibility gaps.

## Planning documents

- docs/PRODUCT_PLAN.md: requirements, page map, permissions, assessment example, and open questions.
- docs/ROADMAP.md: proposed phases, priority rationale, dependencies, and acceptance criteria.
- docs/SETUP.md: tool inventory and future framework setup.
- CONTRIBUTING.md: mandatory branch, commit, and review conventions.
- docs/GITHUB_SETUP.md: repository settings, board proposal, and remote setup status.

## Working conventions

Update these documents as the user confirms decisions. Clearly separate confirmed requirements from proposals.
Inspect actual files, preserve unrelated changes, and use the installed planning skills where useful.
Use a separate branch per feature/task, created from staging. No direct work or pushes to main or staging.
Commit format: type(scope): imperative summary. PRs target staging; release PRs promote staging to main.
Do not claim GitHub protections or deployments are configured until verified remotely.
Do not introduce arbitrary scoring weights or age-based benchmarks as validated standards.
Keep detailed plans in docs rather than growing this file indefinitely.

## Data and verification

Keep credentials, student data, .env, and local database configuration out of Git. Use synthetic fixtures.
Enforce school authorization on every data path, including exports, jobs, files, and direct student URLs.
A browser-provided school ID is not authorization. Use explicit, revocable school memberships with permissions.
Student access should be limited to their own records unless a different policy is approved.
Use parameterized queries, password hashing, server-side validation, and audited result corrections.
For the prototype, PHP syntax checks and local HTTP/database checks are available. Add meaningful automated tests during the framework phase.
Do not run production migrations or import prototype users into the future application without an approved migration plan.
