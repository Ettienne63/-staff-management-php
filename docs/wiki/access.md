# Tenancy, roles and access

> Every school is an isolated tenant. Principal/deputy, HR and students belong to one school; coaches may hold separate memberships at several. Membership is checked on the server for every request; a school ID is never authorization.

**Code:** `app/Http/Middleware/EnsureSchoolAccess.php`, role constants in `app/Models/User.php`, `StaffController`, `StaffRequest`. How it is enforced: [ARCHITECTURE → School isolation](../ARCHITECTURE.md#school-isolation).  
**Last checked:** 2026-11-24 against `6f02660`

## Confirmed

Coaches and students are the initial users. Coaches work with many students in grades 1–12.
Schools are isolated tenants. A coach may receive explicit access to more than one school.
Each principal and deputy is assigned to and may access exactly one school. A coach may be separately hired by multiple schools and receives access to each through an explicit school membership; a coach's access at one school grants no rights at another. Leadership access cannot be used to reach another school, even if an additional membership exists.
Each student belongs to one school at a time. Another school must receive explicit permission before accessing that student's stats; who grants permission, its scope, and revocation rules remain to be defined.
Client and internal platform areas must be separated.

Use individual accounts and school memberships, never a shared school login. Keep Blade and use the installed Tailwind 4 styling pipeline.
Principal and vice/deputy manage the school. HR primarily handles account administration and staff; principal alone controls vice appointments and ownership transfer. Parent and student invitations are implemented. Password resets, general account changes, and audited ownership changes remain future implementation.
Principal is school owner; principal or vice/deputy principal grants access.
Each principal and deputy belongs to one school only. Coaches may belong to multiple schools when each school separately grants access.
Students belong to one school at a time; another school must obtain permission before accessing their stats.
Updated page decisions extend day-to-day account administration to HR; vice appointments and ownership transfer are reserved for the principal.
School ownership never grants rights over another school.

Coaches view authorized school students and have activities, goals, and scheduled training features. Coaches do not have access to staff records, the staff directory, or Family space.

Platform administrators cannot access school staff, students, guardians, invitations, or sports records, even if an old membership or guardian link exists. They register and list schools through separate internal navigation. HR may access one registered school and cannot enter Family space. Existing conflicting single-school memberships use the lowest school ID as the canonical school until the memberships are corrected; records and memberships are preserved.
Audience separation is implemented for leadership (principal/deputy), HR, coaches, students, and the family interface. HR manages staff and parent invitations; coaches and students cannot access staff records. Family space is hidden from principals, deputies, HR, coaches, and students, with the restriction enforced on direct family URLs. Students have dedicated personal training and progress views; their school workspace omits the redundant My schools link. Every family child path verifies an active school-approved guardian relationship. HR cannot inspect child requests or student records. Shared UI components keep the visual design consistent across these separate areas.

Login/logout, a school-selection dashboard, and school-scoped staff CRUD are implemented. Principal/deputy and HR memberships can manage staff; coaches and students cannot view staff records. This is a migration baseline, not a completed student, guardian, or sports workflow. Staff memberships are not granted by creating a staff record; login access is separate.

## Open or proposed

Cross-school permission to see a student's stats (who grants it, scope, revocation) and edit vs view permissions per school: see [PRODUCT_PLAN → Authorization proposal](../PRODUCT_PLAN.md#authorization-proposal).
