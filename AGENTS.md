# Project instructions

## Confirmed product requirements
School sports development for grades 1-12, tracking training, performance, and improvement.
- Each school is an isolated tenant administered by its principal/owner and vice/deputy principal.
- School leadership grants access. Students receive invitation links for account/password setup and can reset passwords.
- Coaches see students in each authorized school and can plan activities, goals, and training sessions.
- Bob is authorized only at Highschool South. Sidney has separate permissions at South and School East.
- Client school/student experiences and internal platform administration must be separated.
- Scores combine participation, performance, and improvement; scoring details remain undecided.
- Guardian accounts and assisted access for children without email are proposals.
- Laravel installation is authorized. Sports features, invitations, and tenancy remain planned until implementation is requested.

## Repository and runtime
Root: C:/xampp/htdocs/my-sports-app. Verify actual files before edits.
Root index.php/app/config contain the plain-PHP prototype, served by XAMPP PHP 8.2.
Laravel 13 foundation: laravel/. PHP 8.5.11 is installed separately and is the default CLI runtime; Composer uses it.
Serve Laravel only from laravel/public using PHP 8.5. Apache access through the legacy site is blocked.
Laravel's SQLite database is disposable foundation data; production database and tenant storage design remain undecided.
Do not import prototype data or migrate the prototype without an approved plan.
No sports features or custom authentication are implemented yet.

## Documents
docs/PRODUCT_PLAN.md: requirements, page map, scoring example, guardian proposal.
docs/ROADMAP.md: priorities, phases, dependencies, acceptance criteria.
docs/SETUP.md: actual runtime paths and commands.
CONTRIBUTING.md: branch and commit conventions.
docs/GITHUB_SETUP.md: verified remote setup status.

## Workflow and verification
Update plans when decisions are confirmed; clearly label proposals.
One task per branch from staging. Task PRs target staging; releases promote staging to main.
No direct pushes to main or staging. Commit format: type(scope): imperative summary.
Preserve unrelated changes; stage explicit files.
Run Laravel tests, Pint, Composer validation, and asset builds for foundation changes.
Starter tests establish framework operation, not sports behavior or tenant isolation.

## Data conventions
Keep credentials, real student data, .env, SQLite files, vendor, node_modules, and generated assets out of Git.
Verify active school memberships and permissions on every query, export, file, and job; a selected school ID is not authorization.
Students access their own records. Guardian links require school verification; matching emails alone must not link children.
Use hashed passwords, expiring single-use invitations, validation, and audited corrections when implemented.
