# School sports app

Laravel 13 application for the planned school sports platform. The prototype's login, dashboard, and staff CRUD now run in Laravel.
School/student training and scoring remain planned. Parent-first onboarding, school-verified guardian links, and separate student invitations are implemented.

## Run locally
Use PHP 8.5 in a new PowerShell terminal:
```powershell
cd C:\xampp\htdocs\my-sports-app
php artisan serve --host=127.0.0.1 --port=8000
```
Open http://127.0.0.1:8000/. The existing prototype login password is preserved in the local import.
Choose your school from `/dashboard`. The signed-in workspace uses a responsive, role-aware sidebar and school dashboard. Principal, vice/deputy, and HR memberships can manage staff; coaches and students cannot access staff records.
Leadership (principal/deputy), HR, coaches, and students have separate dashboard templates. School pages are grouped under `resources/views/schools`; personal student pages and navigation are separate. Students belong to one school at a time; access by another school to their stats will require permission. HR manages staff and sends initial parent invitations but cannot view child requests or student records. Principals, deputies, HR, and coaches cannot access `/family`.
The former localhost/my-sports-app URL is blocked because Apache must serve only public/ using compatible PHP.

School leadership or HR sends a parent invitation from Accounts. The parent sets their own password and submits a child registration/link request in `/family`. A principal or deputy verifies the relationship in Students, choosing an existing student record or creating a new one. An optional student email sends a separate activation invitation; records need no login. Invitations expire after seven days, are single-use, and can be replaced or revoked. Existing parent passwords are preserved. A guardian may view several verified children at different schools; revoking a child link immediately removes access.

Activated students enter their own portal. Training, assessments, scores, family events, password recovery, and student activation without personal email remain planned. The local mailer logs invitation messages; configure delivery before using invitations outside local development. See [setup](docs/SETUP.md).

## Internal administration

Set `PLATFORM_ADMIN_EMAIL` in your local `.env` to the exact email of your existing login account, including letter case. Run `php artisan config:clear`, sign in, and visit `/internal/schools`.
Internal administration lists schools and creates a school with a separate existing account assigned as its first principal. Admins can correct public school contact details, see setup status, inspect platform health and admin activity, and publish expiring notices for principal/deputy dashboards. Admins cannot access private school or family records, even with historical memberships. Leave the setting blank to disable internal access. HR is restricted to one school and cannot access Family space.

Health checks run when the page is loaded; historical uptime monitoring is not connected. Backup status is initially `Not reported`. An external backup process can report success or failure using `php artisan platform:record-backup success` or `failure`; this command does not create or verify a backup. Earlier admin changes are not retroactively audited.

## Structure

| Location | Purpose |
|---|---|
| app/Http/Controllers | Login, internal school onboarding, school workspaces, and staff request handling |
| app/Http/Requests | Staff validation and management authorization |
| app/Http/Middleware | Platform administrator and active school membership checks |
| app/Models | Users, schools, and school-owned staff |
| app/Console/Commands | Local prototype import |
| resources/views | Shared Blade layouts, internal pages, and school pages |
| resources/css | Tailwind styles compiled by Vite |
| routes/web.php | Named web routes |
| database/migrations | Schema changes |
| tests/Feature | Authentication, CRUD, isolation, import regression tests |
| public | Web document root and built assets |
| public/images | Public branding assets, such as the logo |
| storage/app/private | Private files; access must be authorized when upload workflows are added |
| docs | Product decisions, setup, roadmap, and workflow |

There is one Laravel application at the repository root. vendor and node_modules are installed dependencies, not duplicate application code.
Keep `.env`, local SQLite data, dependencies, and generated assets out of Git. The default database seeder creates no accounts or school records; tests create synthetic data through factories.
For a local design walkthrough, run `php artisan db:seed --class=ShowcaseSeeder`. This explicit local-only seeder adds two fictional schools, six preview accounts, and twelve staff records. It prints a generated password for newly created accounts; save it privately. See [showcase setup](docs/SETUP.md#local-showcase-data) for login emails and coverage.
The current logo is `public/images/my-sports-app-logo-Photoroom.png`. If you replace it, update the asset path in the Blade layout. This folder does not need `php artisan storage:link`; that command exposes files stored on the public storage disk.

## Documentation

- [Setup and migration guide](docs/SETUP.md)
- [Hosted demo update guide](docs/HOSTED_UPDATES.md)
- [Product plan](docs/PRODUCT_PLAN.md)
- [Roadmap](docs/ROADMAP.md)
- [Contribution workflow](CONTRIBUTING.md)
- [GitHub status](docs/GITHUB_SETUP.md)
- [Agent instructions](AGENTS.md)

`my-sports-app-demo-1` contains the consolidated Laravel demo. Create separate task branches from it and submit PRs back to it for review and validation. `main` preserves the basic PHP prototype; `staging` remains the previous integration checkpoint.
