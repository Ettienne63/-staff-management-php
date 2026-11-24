# School sports app

Laravel 13 app for school sports development (grades 1–12): schools are isolated tenants, and leadership, HR, coaches, students and guardians each get their own workspace. Training, assessments and scoring are planned, not built.

Codex reads `AGENTS.md`; Claude reads this file. Product facts live in the docs, not in either agent file, so they stay in one place.

## Context: index first

Open `docs/INDEX.md` first, find the task, and read only the page it names. Don't read every doc, and don't open `PRODUCT_PLAN.md` for a confirmed rule: the rules are in `docs/wiki/<topic>.md`. Briefs for subagents name the one or two pages they need, not the whole `docs/` folder.

## Git and branches

- `my-sports-app-demo-1` is the integration branch. Branch from an up-to-date copy of it and target it with PRs. `main` is the basic PHP prototype and `staging` an old checkpoint; touch either only on an explicit request.
- One scope per branch: `feat/`, `fix/`, `docs/` or `chore/` plus a short kebab-case name (see `CONTRIBUTING.md`).
- Commits: `type(scope): imperative summary`. Stage explicit files, never `git add -A`. Keep `.env`, SQLite files, `vendor/`, `node_modules/` and built assets out of Git.
- You may create task branches and commit on your own. **Ask before every push, and before opening or updating a PR.** Never merge, never force-push a shared branch.
- `gh pr create` does not load `.github/pull_request_template.md`: read it and fill in every section yourself.

## Rules that matter most here

- Every school data path checks membership server-side. A school ID in the URL or session is not authorization (see `docs/ARCHITECTURE.md` → School isolation). Each new route under `schools/{school}` gets a feature test showing another school's user gets 404.
- Platform admins never see private school or family data, even with an old membership.
- No real names, schools or student data anywhere: tests use factories, demos use `ShowcaseSeeder`.
- Run `vendor/bin/pint --dirty --format agent` on changed PHP and the narrowest `php artisan test --compact` that covers the change before committing. If PHP isn't available on the machine, say so instead of claiming the checks passed.
- A change to a confirmed rule updates its `docs/wiki/` page in the same commit (and its *Last checked* line). A decision is appended to `docs/decisions.md`; a confirmed proposal moves from `PRODUCT_PLAN.md` to its wiki page. See *Keeping it true* in `docs/INDEX.md`.

## Plans stay local

`/ticket` writes a readable plan per task to `docs/plans/`. Those files are ignored by Git on every machine (only `docs/plans/README.md` is committed): never commit or force-add a plan.

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
