# Product plan

## Page foundation decisions (2026-10-10)

Use individual accounts and school memberships, never a shared school login. Keep Blade and use the installed Tailwind 4 styling pipeline.
Internal platform administration creates schools and assigns their first principal; it does not automatically grant school data access. The administrator will initially be designated by the developer through private configuration; the person is not yet selected. School suspension is deferred.
Principal and vice/deputy manage the school. HR primarily handles account administration and staff; principal alone controls vice appointments and ownership transfer. Invitations, resets, account changes, and audited ownership changes remain future implementation.
Coaches may edit training and results for the students they are authorized to see; these sports workflows are not implemented in the page foundation.
Students will view their own training, goals, and progress and may mark exercises complete. Evidence links, such as Google Docs, are a future feature.
First delivery focuses on protected internal/client page foundations, basic school creation, and honest empty states. No fictional performance data or working invitation controls are presented.

Status: prototype login and school-scoped staff workflows are migrated to Laravel; sports features remain planned.

## Confirmed

Coaches and students are the initial users. Coaches work with many students in grades 1–12.
Schools are isolated tenants. A coach may receive explicit access to more than one school.
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
Internal area: school/account administration and support, separated from client routes and permissions.
Propose no routine internal access to student results; exceptional support access should be explicit, limited, and auditable.

## Authorization proposal

Coach identity is shared across schools, but school memberships and role permissions are separate.
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
Student transfers and identity linking across schools need an explicit policy.

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
Students receive signup invitations and password reset support.
Coaches view authorized school students and have activities, goals, and scheduled training features.
The single-school and multi-school coach names in discussion were fictional examples only; no such accounts or schools are seeded.
School ownership never grants rights over another school.

## Confirmed guardian-linked account design
Separate student records from account identities. An email's owner does not define who the student is.
With personal email: invite the student to claim a school-selected student record using an expiring single-use link.
Without personal email: school verifies a guardian relationship and invites the guardian's own account to access that child's permitted records.
One guardian may have several children; siblings never share a student identity.
No email at all: keep the student record; propose school-assisted activation/recovery through a verified contact.
Do not invent emails or distribute shared passwords. Invitations and password resets are distinct flows.
Use hashed tokens, expiry, rate limits, generic reset responses, and audited access changes when implemented.
Guardian login clearly identifies the selected child and does not impersonate the student.
Define guardian revocation, relationship verification, and later transition to independent student access.
Existing prototype authentication is migrated. Guardian linking, invitations, and password reset delivery remain unimplemented.

## Migrated prototype scope
Login/logout, a school-selection dashboard, and school-scoped staff CRUD are implemented. Principal/deputy memberships can manage staff; coaches can view staff. This is a migration baseline, not a completed student, guardian, or sports workflow. Staff memberships are not granted by creating a staff record; login access is separate.
School-assisted activation for families without email remains a proposal. The school verifies guardian links before granting access.
