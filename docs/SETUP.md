# Development setup

Checked 2026-10-10.

## Installed
- PHP 8.5.11: C:/Users/ettie/AppData/Local/sports-dev-tools/php85/php.exe.
- Composer 2.10.3: C:/Users/ettie/AppData/Local/sports-dev-tools/composer.cmd (uses PHP 8.5).
- Node.js 24.20.0 / npm 11.19.0.
- Laravel framework 13.35.0 in laravel/, with Composer lockfile.
- Vite/Tailwind foundation with npm lockfile, PHPUnit starter tests, and Laravel Pint.
- GitHub CLI authenticated as Ettienne63. Repository Git identity uses the verified profile name and GitHub noreply address.

Restart VS Code/terminals after the user PATH update.
XAMPP Apache still uses PHP 8.2.12 for the old prototype. The new Laravel app uses PHP 8.5 via its development server.
The downloaded NTS runtime must not be substituted directly for XAMPP's Apache PHP module.

## Run Laravel
In a new PowerShell terminal:
```powershell
cd C:\xampp\htdocs\my-sports-app\laravel
php --version
php artisan serve --host=127.0.0.1 --port=8000
```
Open http://127.0.0.1:8000/. Keep that terminal running; Ctrl+C stops it.
Assets are compiled. For live frontend work, run npm.cmd run dev in a second terminal inside laravel/.
The app shows Laravel's default welcome page; sports pages and invitations are not implemented.

## Fresh checkout
Run from laravel/ after the foundation PR is merged:
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
These assume a fresh checkout. Do not overwrite an existing .env or recreate an existing database.
SQLite is isolated local foundation data, not the final production/tenant database.
The default local mailer logs output; no invitations or external emails are sent.

## Verify changes
From laravel/:
```powershell
composer validate --strict
composer check-platform-reqs
php artisan test
php vendor/bin/pint --test
npm.cmd run build
```

## Legacy prototype and remaining choices
Start Apache/MySQL in XAMPP; open http://localhost/my-sports-app/.
Root config/database.php remains ignored. The parent .htaccess blocks the laravel/ subtree from legacy Apache access.
A future Apache/production server must point to laravel/public and run supported PHP.
Frontend starter kit, production database, tenant storage, mail delivery, and hosting remain undecided.

References:
- https://laravel.com/framework/docs/13.x/installation
- https://laravel.com/framework/docs/13.x/deployment
- https://www.php.net/downloads.php?os=windows
