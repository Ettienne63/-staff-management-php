# Product plan

## Page foundation decisions (2026-10-10)

Use individual accounts and school memberships, never a shared school login. Keep Blade and use the installed Tailwind 4 styling pipeline.
Internal platform administration creates schools and assigns their first principal using a separate existing account. It cannot access private school data, regardless of stored memberships. The administrator will initially be designated by the developer through private configuration; the person is not yet selected. School suspension is deferred.
Principal and vice/deputy manage the school. HR primarily handles account administration and staff; principal alone controls vice appointments and ownership transfer. Parent and student invitations are implemented. Password resets, general account changes, and audited ownership changes remain future implementation.
Coaches may edit training and results for the students they are authorized to see; these sports workflows are not implemented in the page foundation.
Students will view their own training, goals, and progress and may mark exercises complete. Evidence links, such as Google Docs, are a future feature.
The page foundations now include parent-first onboarding and verified child profiles. Sports workflows retain honest empty states and no fictional performance data is presented.

Status: prototype login and school-scoped staff workflows are migrated to Laravel; sports features remain planned.

## Confirmed dashboard design (2026-10-10)

The user approved showcase fixtures on the dedicated hosted demo (2026-10-11). `SHOWCASE_ENABLED=true` explicitly permits the existing seeder outside local/testing while keeping production mode and debug disabled. It defaults to false, preserves passwords/memberships on reruns, creates no child records or guardian links, and does not change empty default seeding.

An explicit `showcase:password` command lets the user choose or replace passwords only for unchanged showcase login accounts through a hidden prompt. Hosted use requires demo enablement. This is separate from seeding; reruns never reset existing credentials. No plaintext credential is committed, logged, or printed by the password command.

Platform administrators cannot access school staff, students, guardians, invitations, or sports records, even if an old membership or guardian link exists. They register and list schools through separate internal navigation. HR may access one registered school and cannot enter Family space. Existing conflicting single-school memberships use the lowest school ID as the canonical school until the memberships are corrected; records and memberships are preserved.

Confirmed platform tools are implemented: public school name/email/phone/website corrections, setup indicators for principal assignment and public contact availability, on-demand application/database/storage health, platform activity history, and expiring announcements on principal/deputy dashboards. Setup indicators expose no personal identities, counts of students, or sports activity. Activity history contains only platform operations and public detail corrections; principal assignment email is not logged. Notices are plain text with explicit expiry and withdrawal; no email is sent.

Backup status shows the latest success/failure reported by an external backup process through `platform:record-backup`. Until a process is connected it remains unreported. Backup creation, restore verification, historical uptime monitoring, and alert delivery remain future operational integration work.

Authenticated school pages use a responsive, role-aware sidebar and a school workspace with quick links and a daily schedule panel. The visual direction follows the logo's navy, teal, and green colors on a pale blue background. Empty schedule and activity states do not imply that training or student workflows are implemented.

Audience separation is implemented for leadership (principal/deputy), HR, coaches, students, and the family interface. HR manages staff and parent invitations; coaches and students cannot access staff records. Family space is hidden from principals, deputies, HR, and coaches. Students have dedicated personal training and progress views. Every family child path verifies an active school-approved guardian relationship. HR cannot inspect child requests or student records. Shared UI components keep the visual design consistent across these separate areas.

## Confirmed

Coaches and students are the initial users. Coaches work with many students in grades 1–12.
Schools are isolated tenants. A coach may receive explicit access to more than one school.
Each principal and deputy is assigned to and may access exactly one school. A coach may be separately hired by multiple schools and receives access to each through an explicit school membership; a coach's access at one school grants no rights at another. Leadership access cannot be used to reach another school, even if an additional membership exists.
Each student belongs to one school at a time. Another school must receive explicit permission before accessing that student's stats; who grants permission, its scope, and revocation rules remain to be defined.
Client and internal platform areas must be separated.
Scores will combine training participation, measured performance, and improvement; details will be refined later.

## Proposed first experience

1. Coach signs in and chooses an authorized school.
2. Coach adds students individually or imports a roster, then assigns grade/year and training groups.
3. Coach records a baseline assessment using a defined measurement protocol.
4. Coach assigns a training program and records completion.
5. Coach repeats the same assessment after an agreed interval.
6. Student and coach view raw measurements and changes, with any provisional scores explained.

Roster upload format is not confirmed. Propose CSV with preview, validation, duplicates, and an explicit confirm step.
Students should not require an email address just to have a record. Separate the student record from an optional login account.

## Proposed page map

Client school workspace:

- Dashboard: selected school, upcoming sessions, recent assessments, incomplete records.
- Students: search, grade/year and group filters, add/import actions.
- Student profile: training assignments, result history, progress chart, score explanation.
- Training programs: templates, assignments, sessions, completion.
- Assessments: assessment definitions and batch result entry.
- Reports: student progress, with exports restricted by authorization.
- School switcher: visible only for multiple active school memberships.

Student area: invitation signup/password setup, own progress, and assigned training. Ability to enter results is undecided.
Internal area: platform school onboarding and initial principal assignment, separated from client account administration and permissions.
Propose no routine internal access to student results; exceptional support access should be explicit, limited, and auditable.

## Authorization proposal

Coach identity is shared across schools, but school memberships and role permissions are separate. Each school independently hires or assigns the coach; a coach may serve teams at multiple schools. Principals and deputies have one school membership and cannot switch to another school's workspace.
Students have one school membership at a time. Cross-school statistics sharing requires permission and must be authorized separately from a current school membership.
Selecting a school narrows the workspace; the server verifies membership and permission on every request.
A membership at School A gives no rights at School B. Revoking B access takes effect for B data.
Each school independently grants the coach role and its capabilities. Read-only access can be a separate permission where needed.
Confirmed: coaches can view the students at each school they are authorized to access.
An aggregate across schools, if later needed, must independently authorize every contributing record.

Acceptance examples:

- A coach assigned only to School A cannot read School B by changing URLs or IDs.
- A coach with A edit access and B view access can record A results but cannot edit B results.
- A student cannot view another student's record.
- Search, imports, exports, jobs, and files apply the same access policy.

## Proposed entities

School; User; SchoolMembership; Student; Enrollment/GradeYear; CoachStudentAssignment;
Sport; TrainingProgram; ProgramAssignment; TrainingSession; Completion;
AssessmentDefinition; AssessmentResult; ScoringRuleVersion; AuditEvent.
These are conceptual entities, not approved schema or migrations.
Keep academic year and historical grade separate from a student's current grade so promotion does not rewrite history.
Student transfers and identity linking across schools need an explicit policy. Define who can approve another school's access to past stats, which stats may be shared, how long access lasts, and how it can be revoked.

## Illustrative assessment: sprint progress

This is a product example, not a prescribed training program or a validated scoring standard.
Use a coach-approved sprint distance appropriate to the student group and record timing method, surface, date, and unit.
Example baseline: 20 metres in 4.8 seconds. Reassessment: 4.5 seconds.
Lower time is better: (4.8 - 4.5) / 4.8 × 100 = 6.25% improvement.
Show both raw results and the percentage; do not label an overall score as scientifically validated.
Record comparable conditions. Missing baseline, zero baseline, invalid measurements, and changed protocols must not produce a misleading improvement percentage.
For a higher-is-better measurement, use (new - baseline) / baseline × 100.

Proposed score components:

- Participation: completed eligible sessions / eligible scheduled sessions; approved absences need a policy.
- Performance: measured result against approved category-specific criteria.
- Improvement: change from a valid personal baseline under comparable conditions.
  Start by displaying these components separately. A combined score needs approved weights and normalization before implementation.
  Avoid fabricated benchmarks, comparisons between unrelated sports, or penalizing younger students by default.
  Store rule versions so historical scores can be reproduced. Corrections must retain an audit trail.

## Decisions needed next

1. Define guardian verification and assisted access for families without email.
2. Define school onboarding and owner/deputy handover; principal/deputy manage school access.
3. Confirm editing permissions beyond school-wide viewing.
4. Does upload mean CSV, spreadsheet, manual entry, or integration with a school system?
5. First pilot sport, age/grade group, and measurement protocol?
6. Academic year convention, roster scale, connectivity, and expected number of schools?
7. Program completion, approved absences, reassessment interval, and result correction ownership?
8. Hosting, retention, school/guardian permissions, and export rules?
9. Internal versus client roles and navigation?
10. Final database engine and tenant storage model?

## Confirmed access decisions (2026-10-10)

Principal is school owner; principal or vice/deputy principal grants access.
Each principal and deputy belongs to one school only. Coaches may belong to multiple schools when each school separately grants access.
Students belong to one school at a time; another school must obtain permission before accessing their stats.
Updated page decisions extend day-to-day account administration to HR; vice appointments and ownership transfer are reserved for the principal.
Students receive signup invitations and password reset support.
Coaches view authorized school students and have activities, goals, and scheduled training features. Coaches do not have access to staff records, the staff directory, or Family space.
The single-school and multi-school coach names in discussion were fictional examples only. Separately approved local showcase fixtures use explicit Showcase names and reserved `.invalid` emails: two schools, staff directories, and principal, deputy, HR, coach, student, and guardian preview accounts. They contain no student roster records, guardian links, or performance data. Default seeding remains empty.
School ownership never grants rights over another school.

## Confirmed guardian-linked account design

Separate student records from account identities. An email's owner does not define who the student is.
Parent-first onboarding is the confirmed direction: the parent receives the initial login and submits a request to register or link a child. The school must verify the child and guardian relationship before granting access to child records. An existing school student record must be linked rather than duplicated; submitting a request alone grants no access.
Student activation is implemented: after school verification, leadership invites a student with their own unused email to create a separate account linked to that same child record. The parent and student keep separate logins and permissions. Student login without a personal email and recovery flows remain to be decided; the child record does not require a login.
With personal email: invite the student to claim a school-selected student record using an expiring single-use link.
Without personal email: school verifies a guardian relationship and invites the guardian's own account to access that child's permitted records.
One guardian may have several children; siblings never share a student identity.
No email at all: keep the student record; propose school-assisted activation/recovery through a verified contact.
Do not invent emails or distribute shared passwords. Invitations and password resets are distinct flows.
Use hashed tokens, expiry, rate limits, generic reset responses, and audited access changes when implemented.
Guardian login clearly identifies the selected child and does not impersonate the student.
School leadership manually verifies the relationship, can reject requests with a reason, and can revoke guardian access to each child immediately. School reviewers explicitly select an existing record; names or shared emails never automatically link identities. Review decisions, invitations, acceptance, and guardian revocation are audited.
Parent and student invitations use hashed tokens, seven-day expiry, single-use acceptance, rate limiting, and revocation. Resending replaces previous unused links. Existing parent accounts must sign in to accept and keep their passwords. Student accounts require separate emails and are linked to exactly one student and school. Password reset delivery remains unimplemented.
Accounts without a school workspace membership enter the family portal after login; designated platform administrators keep their platform entry. Only an accepted parent invitation grants permission to submit requests to that school. This enrollment itself grants no child access; the verified child relationship is separate.

## Proposed guardian portal direction

The guardian portal focuses on children's progress and family events or updates. Verified profiles and onboarding requests are implemented. Assessments, scores, and event information remain empty foundations.

## Migrated prototype scope

Login/logout, a school-selection dashboard, and school-scoped staff CRUD are implemented. Principal/deputy and HR memberships can manage staff; coaches and students cannot view staff records. This is a migration baseline, not a completed student, guardian, or sports workflow. Staff memberships are not granted by creating a staff record; login access is separate.
School-assisted activation for families without email remains a proposal. The school verifies guardian links before granting access.
