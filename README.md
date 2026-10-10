# School sports development app

Planning repository for an intended Laravel application helping schools track student sports training and progress.
The root contains the existing plain PHP prototype. A Laravel 13 foundation is installed in laravel/; sports features are not implemented.

## Start here

- [Product plan](docs/PRODUCT_PLAN.md)
- [Roadmap](docs/ROADMAP.md)
- [Tooling and setup](docs/SETUP.md)
- [Contribution workflow](CONTRIBUTING.md)
- [GitHub setup](docs/GITHUB_SETUP.md)
- [Agent instructions](AGENTS.md)

## Run the existing prototype

1. Start Apache and MySQL in XAMPP.
2. Keep this repository at C:/xampp/htdocs/my-sports-app.
3. For a fresh local database only, import database/setup.sql using phpMyAdmin.
4. Copy config/database.example.php to config/database.php and set your local credentials.
5. Open http://localhost/my-sports-app/.

Do not reimport the SQL file into a configured database without reviewing it: it updates seed records, including the example administrator's password.
The example administrator is local prototype data, not a production account. Passwords are not documented here.
The .htaccess routes require Apache rewrite support; PHP's basic development server alone does not reproduce these routes.
Laravel dependencies and commands live in laravel/. Follow docs/SETUP.md using PHP 8.5.

## Development workflow

One task per branch → PR into staging → validate → release PR into main.
See CONTRIBUTING.md for naming, commit format, and review checks.

## Run the new foundation
From laravel/, run php artisan serve --host=127.0.0.1 --port=8000 using PHP 8.5.
Open http://127.0.0.1:8000/. See docs/SETUP.md for complete instructions.
