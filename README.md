# School sports app

Laravel 13 application for the planned school sports platform. The prototype's login, dashboard, and staff CRUD now run in Laravel.
School/student training, scores, invitations, and guardian access are documented requirements; those sports features are not implemented yet.

## Run locally
Use PHP 8.5 in a new PowerShell terminal:
```powershell
cd C:\xampp\htdocs\my-sports-app
php artisan serve --host=127.0.0.1 --port=8000
```
Open http://127.0.0.1:8000/. The existing prototype login password is preserved in the local import.
Choose your school from `/dashboard`. The signed-in workspace uses a responsive, role-aware sidebar and school dashboard. Principal, vice/deputy, and HR memberships can manage staff; coaches and students cannot access staff records.
Training, progress, student rosters, and account administration currently show clearly labelled placeholders for future workflows.
Leadership (principal/deputy), HR, coaches, and students have separate dashboard templates. School pages are grouped under `resources/views/schools`; personal student pages and navigation are separate. Students belong to one school at a time; access by another school to their stats will require permission. HR is limited to staff and account administration. `/family` provides the separate guardian interface as an authenticated empty foundation; principals, deputies, and coaches cannot access it, and it contains no linked child records yet.
The former localhost/my-sports-app URL is blocked because Apache must serve only public/ using compatible PHP.

The guardian portal is organized around children's progress and family events, with no school workspace section. Those areas remain empty foundations until guardian links and event workflows are implemented.

## Internal administration

Set `PLATFORM_ADMIN_EMAIL` in your local `.env` to the exact email of your existing login account, including letter case. Run `php artisan config:clear`, sign in, and visit `/internal/schools`.
Internal administration lists schools and creates a school with an existing account assigned as its first principal. Platform access does not automatically grant school membership. Leave the setting blank to disable internal access.

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
- [Product plan](docs/PRODUCT_PLAN.md)
- [Roadmap](docs/ROADMAP.md)
- [Contribution workflow](CONTRIBUTING.md)
- [GitHub status](docs/GITHUB_SETUP.md)
- [Agent instructions](AGENTS.md)

Task branch → PR to staging → validation → release PR to main.
