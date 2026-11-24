# Platform administration

> One designated account (`PLATFORM_ADMIN_EMAIL`) registers schools and runs internal tools under `/internal`. It never sees private school or family data, even with old memberships.

**Code:** `app/Http/Middleware/EnsurePlatformAdmin.php`, `InternalSchoolController`, `PlatformController`, `app/Console/Commands/RecordBackupResult.php`, views in `resources/views/internal`. Tests: `InternalSchoolTest`, `PlatformAccessTest`, `PlatformToolsTest`.  
**Last checked:** 2026-11-24 against `6f02660`

## Confirmed

Internal platform administration creates schools and assigns their first principal using a separate existing account. It cannot access private school data, regardless of stored memberships. The administrator will initially be designated by the developer through private configuration; the person is not yet selected. School suspension is deferred.

Confirmed platform tools are implemented: public school name/email/phone/website corrections, setup indicators for principal assignment and public contact availability, on-demand application/database/storage health, platform activity history, and expiring announcements on principal/deputy dashboards. Setup indicators expose no personal identities, counts of students, or sports activity. Activity history contains only platform operations and public detail corrections; principal assignment email is not logged. Notices are plain text with explicit expiry and withdrawal; no email is sent.

Backup status shows the latest success/failure reported by an external backup process through `platform:record-backup`. Until a process is connected it remains unreported. Backup creation, restore verification, historical uptime monitoring, and alert delivery remain future operational integration work.
