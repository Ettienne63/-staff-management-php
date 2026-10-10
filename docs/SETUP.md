# Development setup and prototype migration

## Runtime
Laravel 13.35.0 now lives at the repository root; the temporary laravel/ folder has been removed.
PHP 8.5.11: C:/Users/ettie/AppData/Local/sports-dev-tools/php85/php.exe.
Composer 2.10.3 uses that PHP runtime. Node 24.20.0/npm 11.19.0 provide the asset build.
Restart VS Code/terminals after PATH changes.
Use http://127.0.0.1:8000/ with php artisan serve from the project root.
Apache/XAMPP remains on PHP 8.2 and is not used to serve Laravel.

## Fresh local checkout
```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
New-Item -ItemType File database/database.sqlite
php artisan migrate
npm.cmd ci
npm.cmd run build
php artisan serve --host=127.0.0.1 --port=8000
```
These assume a fresh checkout; preserve existing .env and database files.
Local SQLite is development storage. Production database/storage isolation remains undecided.
Configure an account/membership through an approved onboarding path when that is implemented; public signup is not enabled.

## Prototype archive and local import
The original PHP prototype, SQL, assets, and local database configuration are archived outside the web root:
C:/Users/ettie/AppData/Local/sports-dev-tools/prototype-backup-20261010.
The archived code is also recoverable from Git history. Credentials are not committed.
Its MariaDB database is left unchanged.

Local-only import command:
```powershell
php artisan prototype:import --config="C:\Users\ettie\AppData\Local\sports-dev-tools\prototype-backup-20261010\config\database.php" --school="Prototype school"
```
The --config must be a trusted local PHP file defining a PDO connection. It is executed locally; never use uploaded or untrusted configuration.
The command reads the source, validates records, and writes transactionally to the destination.
It refuses production execution and ambiguous destination school names.
Prototype users were administrators; newly imported accounts receive principal access to the explicitly selected school only.
Passwords remain hashed. Existing destination accounts, staff edits, and memberships are preserved on repeated imports.
The current local import transferred one account and two staff records.
Future production migrations need a separate plan.

## Internal administration foundation

To enable internal school administration for your existing login, set `PLATFORM_ADMIN_EMAIL` to that account's exact email, including letter case, in your local `.env`. Leave it blank to disable internal access. Run `php artisan config:clear` after changing it (or rebuild the configuration cache in deployments).
Visit `/internal/schools` after signing in. School creation assigns an existing account as the initial principal; it does not create accounts or send invitations. Platform access grants no automatic school membership. Do not reuse a designated admin email for a different account. Managed platform roles and audited handover are future work.

Client entry is `/dashboard`; `/admin/dashboard` redirects there for compatibility. Selecting a school opens `/schools/{school}` with the view for that membership's principal, deputy, HR, coach, or student role. Student pages are empty foundations and expose no staff directory or student records. HR and school leadership can manage staff records; coaches cannot access the staff directory. Account invitation and role-assignment tools are still unavailable. Default seeding remains empty; the optional showcase below requires an explicit command.

## Local showcase data

Seed data initializes a database with known records for setup or manual exploration. Automated tests instead create temporary fixtures in their isolated test database.

With PHP 8.5 active, run:

```powershell
php artisan db:seed --class=ShowcaseSeeder
```

This command is allowed only in `local` and `testing` environments, even with `--force`. It adds fictional Showcase Oakfield and Showcase Riverbend schools, six staff records per school, and these login accounts:

| Email | Preview |
| --- | --- |
| principal@showcase.example.invalid | Leadership dashboard and staff CRUD in one school |
| deputy@showcase.example.invalid | Deputy leadership dashboard and staff CRUD in one school |
| hr@showcase.example.invalid | HR dashboard, staff CRUD, and account placeholder in one school |
| coach@showcase.example.invalid | Coach dashboard and sports placeholders in both schools; no staff directory access |
| student@showcase.example.invalid | Personal dashboard, training, and progress placeholders |
| guardian@showcase.example.invalid | `/family` empty foundation, with no school memberships |

The command prints a random shared password for accounts created in that run. Save it privately; there is no committed default password. Rerunning creates missing fixtures without resetting existing passwords, changing existing account memberships, or deleting records. New principal, deputy, and HR preview memberships cover one school; the coach has a separate membership at both schools. If a matching email already exists, its account is preserved and no memberships are added to it. Reserved `.invalid` addresses cannot receive invitations or password-reset emails.

The existing `PLATFORM_ADMIN_EMAIL` setting is preserved. Sign in with the configured administrator to inspect the internal school directory and creation form, now including the showcase schools. Platform access is not granted to preview accounts automatically.

Students, guardian links, training sessions, assessments, scores, invitations, and account management remain unimplemented; these pages retain their labelled empty states. The showcase student is a login account, not a student roster record. The guardian login has no verified child links. Staff records and login accounts are separate entities.

## Verify
```powershell
composer validate --strict
composer check-platform-reqs
php artisan test --compact
php vendor/bin/pint --dirty --format agent
npm.cmd run build
```
CI installs locked dependencies, runs formatting/tests, and builds assets.
Tests use synthetic data and an isolated in-memory SQLite database.

## Web document root
Serve only public/ under PHP 8.5. Root .htaccess denies legacy Apache access to source/configuration.
public/index.php is Laravel's front controller. There is no root prototype index.php.
Local database credentials belong in .env. config/database.php is now Laravel's safe committed database configuration.
The mailer logs locally; signup invitations/reset delivery and guardian access remain future work.

References:
- https://laravel.com/framework/docs/13.x/installation
- https://laravel.com/framework/docs/13.x/deployment
