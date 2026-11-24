# Project instructions

## Product requirements
Start at docs/INDEX.md and open only the page the task needs. Confirmed product rules live in docs/wiki/, one page per topic, the single source both Codex and Claude read; docs/PRODUCT_PLAN.md holds only the vision, proposals and open decisions. When a rule changes, update its wiki page in the same commit, not this file, and append the decision to docs/decisions.md. See docs/ARCHITECTURE.md for where rules are enforced in code and docs/laravel-standards.md for the code conventions.

## Implementation and runtime
One Laravel 13 app at the repository root, started 2026-11-24 from the sports app template on its own orphan branch. Use PHP 8.5 and Composer; Node/npm compile assets. Serve public/ only.
See docs/SETUP.md for paths and actual commands.

## Workflow and docs
`my-sports-app-demo-2` is this app's integration branch. Create task branches from it and target it with task PRs. Other demos in this repository are unrelated apps on their own branches; never merge between them.
No direct pushes to protected branches. Use type(scope): imperative summary.
Preserve user changes; stage explicit files. Commit focused, useful increments.
Keep each branch focused on one feature, bug fix, documentation task, or maintenance task. Use feat/, fix/, docs/, or chore/ names matching the work. If a task depends on unmerged work, record that dependency instead of silently mixing scopes. When checks pass and a PR is ready for my-sports-app-demo-2, notify the user and wait for explicit merge approval; permission to commit or push a task branch does not authorize merging.
Commit completed, reviewable increments after the applicable checks pass. Use fix for correcting existing behavior, feat for new capabilities, docs for documentation, refactor for behavior-preserving restructuring, test for coverage, and chore/build/ci for the corresponding maintenance or tooling work.
Update the docs/wiki/ page (confirmed rules), docs/PRODUCT_PLAN.md (proposals) or docs/ROADMAP.md when decisions change. Mark proposals clearly.
README.md explains the canonical structure; CONTRIBUTING.md defines collaboration rules.
No example names are actual customer records.

## Code comments
Do not add routine comments when implementing features; prefer clear names and straightforward code.
Add a brief comment only when sensitive behavior, a non-obvious constraint, or a maintenance caveat needs explanation.
Explain why the detail matters or what must be preserved when changing it; do not narrate what the code already says.
Keep necessary PHPDoc type annotations concise and avoid redundant documentation blocks.

## Data and validation
Keep credentials, real personal data, .env, SQLite files, dependencies, and built assets out of Git.
config/database.php is Laravel configuration and is committed; secrets live in .env.
Check authorization server-side on every data path. An ID supplied by the client is not authorization.
Use password hashing, expiring single-use tokens, CSRF protection, validation, and audited corrections.
Run meaningful feature tests for each protected path, and also run Pint, Composer checks, and builds.

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

## Foundational Context

This application is a Laravel application running on PHP 8.5. Always use the APIs that match the installed major version of each package — do not assume a version.

Before relying on a package's API, confirm its installed version:
- PHP packages: run `composer show --direct` to list direct dependencies with versions, or `composer show <vendor/package>` for a single package.
- JS packages: check `package.json` for the installed versions.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If a frontend change doesn't show in the UI or you get a "Unable to locate file in Vite manifest" error, run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

=== boost rules ===

# Laravel Boost

## Project Rules

- This project contains committed, area-grouped rules in `.ai/rules` when that directory exists, including path-scoped framework guidelines under `.ai/rules/boost`. Before you enter plan mode or create/edit any file, you MUST first: open @.ai/rules/index.md (it maps file globs to rule files), read every rule file whose globs cover the path(s) in scope, and run `grep -rin 'keyword' .ai/rules` to catch what a path match alone misses. Do not write code until you have read and are following every matching rule. If `.ai/rules` does not exist, continue without it.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== phpunit/core rules ===

# PHPUnit

- This project uses PHPUnit. Create tests with `php artisan make:test --phpunit {name}`.
- Do not include the test suite directory in `{name}`. Use `SomeFeatureTest`, not `Feature/SomeFeatureTest`.
- Read the `testing-best-practices` skill for guidance on coverage, naming, structure, dependency isolation, and review.

## Running Tests

- Run the narrowest set of tests that covers the change. Pass a file path or `--filter=testName` to `php artisan test --compact`.
- Rerun a test after each change to it.
- Run `vendor/bin/phpunit` to call the test runner directly. It accepts the same file path and `--filter=testName` arguments.

</laravel-boost-guidelines>
