# Showcase and demo data

> Default seeding is empty. `ShowcaseSeeder` adds fictional schools and preview accounts only on request locally, or on the hosted demo with `SHOWCASE_ENABLED=true`. Passwords change only through `showcase:password`.

**Code:** `database/seeders/ShowcaseSeeder.php`, `app/Console/Commands/SetShowcasePassword.php`, `config/app.php` (`showcase_enabled`). Setup steps: [SETUP → Local showcase data](../SETUP.md#local-showcase-data). Tests: `ShowcaseSeedingTest`, `ShowcasePasswordTest`.  
**Last checked:** 2026-11-24 against `6f02660`

## Confirmed

The user approved showcase fixtures on the dedicated hosted demo (2026-10-11). `SHOWCASE_ENABLED=true` explicitly permits the existing seeder outside local/testing while keeping production mode and debug disabled. It defaults to false, preserves passwords/memberships on reruns, creates no child records or guardian links, and does not change empty default seeding.

An explicit `showcase:password` command lets the user choose or replace passwords only for unchanged showcase login accounts through a hidden prompt. Hosted use requires demo enablement. This is separate from seeding; reruns never reset existing credentials. No plaintext credential is committed, logged, or printed by the password command.

The single-school and multi-school coach names in discussion were fictional examples only. Separately approved local showcase fixtures use explicit Showcase names and reserved `.invalid` emails: two schools, staff directories, and principal, deputy, HR, coach, student, and guardian preview accounts. They contain no student roster records, guardian links, or performance data. Default seeding remains empty.
