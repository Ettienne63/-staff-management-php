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
Choose your school from the dashboard. Principal/deputy memberships manage staff; coaches can view school staff.
The former localhost/my-sports-app URL is blocked because Apache must serve only public/ using compatible PHP.

## Structure
| Location | Purpose |
|---|---|
| app/Http/Controllers | Login, school selection, and staff request handling |
| app/Http/Requests | Staff validation and management authorization |
| app/Http/Middleware | Active school membership checks |
| app/Models | Users, schools, and school-owned staff |
| app/Console/Commands | Local prototype import |
| resources/views | Shared Blade layout and page templates |
| resources/css | Application styles compiled by Vite |
| routes/web.php | Named web routes |
| database/migrations | Schema changes |
| tests/Feature | Authentication, CRUD, isolation, import regression tests |
| public | Web document root and built assets |
| docs | Product decisions, setup, roadmap, and workflow |

There is one Laravel application at the repository root. vendor and node_modules are installed dependencies, not duplicate application code.

## Documentation
- [Setup and migration guide](docs/SETUP.md)
- [Product plan](docs/PRODUCT_PLAN.md)
- [Roadmap](docs/ROADMAP.md)
- [Contribution workflow](CONTRIBUTING.md)
- [GitHub status](docs/GITHUB_SETUP.md)
- [Agent instructions](AGENTS.md)

Task branch → PR to staging → validation → release PR to main.
