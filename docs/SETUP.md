# Tooling and framework introduction

Inventory checked 2026-10-10.

| Item                         | Current status                                                                   | Next action                                             |
| ---------------------------- | -------------------------------------------------------------------------------- | ------------------------------------------------------- |
| Git                          | Installed                                                                        | Use task branches from staging                          |
| PHP                          | XAMPP 8.2.12 with PDO MySQL, mbstring, OpenSSL, cURL and other common extensions | Plan a supported newer runtime before Laravel 13        |
| Apache/MariaDB               | Existing XAMPP prototype runtime                                                 | Keep current prototype working                          |
| Node.js/npm                  | Installed: Node 24.20.0, npm 11.19.0                                             | Added existing Node folder to user PATH                 |
| Composer                     | Installed: 2.10.3                                                                | User tools directory; no project dependencies installed |
| GitHub CLI                   | Installed: 2.102.0; not authenticated                                            | Needed for remote settings and PR operations            |
| Laravel                      | Absent                                                                           | Introduce after architecture approval                   |
| Frontend and test frameworks | Not configured                                                                   | Select during foundation phase                          |
| Production database          | Undecided                                                                        | Compare tenant requirements before selecting            |

Composer location: C:/Users/ettie/AppData/Local/sports-dev-tools/composer.phar.
A composer.cmd wrapper uses the existing XAMPP PHP executable.
Restart the terminal/VS Code after PATH changes.
Verify with php --version, composer --version, node --version, npm.cmd --version.

Laravel 13 requires PHP 8.3 or later. Current PHP 8.2.12 is incompatible with it.
Do not silently select an older Laravel release just to fit the prototype runtime.
Proposed next step: choose a current supported PHP runtime alongside XAMPP; confirm web and CLI versions agree.
No runtime upgrade or Laravel scaffolding occurs in the planning phase.

Future framework introduction checklist:

1. Select PHP/Laravel, database, tenant storage strategy, and frontend approach.
2. Prepare an isolated development database using synthetic fixtures.
3. Create the framework on a task branch; review the prototype migration approach.
4. Introduce composer.json/composer.lock and package.json/package lockfile.
5. Configure local .env without committing secrets; commit a safe .env.example.
6. Configure the web server to serve Laravel's public directory, not its repository root.
7. Document actual install/run/test/build commands and CI checks.
8. Plan staging deployment and data isolation separately from the staging Git branch.

Sources:

- https://laravel.com/framework/docs/13.x/installation
- https://laravel.com/framework/docs/13.x/releases
- https://getcomposer.org/download/
