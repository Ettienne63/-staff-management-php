# Glossary

> The words this project uses, what each means here, and where it lives in code. Read this instead of guessing from names.

**Last checked:** 2026-11-24

| Term | Means here | In code |
|---|---|---|
| School | The tenant. All private data belongs to one school. | `School`, `schools` |
| Membership | A user's role at one school. Grants nothing at any other school. | `school_user` pivot, `role` column |
| Leadership | Principal and vice/deputy principal. | `User::LeadershipRoles` |
| Management | Leadership plus HR: may manage staff and invite parents. | `User::ManagementRoles` |
| Single-school role | Principal, deputy, HR, student: only one school. | `User::SingleSchoolRoles` |
| Staff member | A staff *record*. Not a login; creating one grants no access. | `StaffMember`, `staff_members` |
| Student record | The child as the school knows them. Needs no login or email. | `Student`, `students` |
| Student account | A login a student activates with their own email, linked to exactly one student record. | `students.user_id` |
| Guardian | A parent or legal guardian account. Sees a child only through a verified, unrevoked link. | `guardian_student` pivot, `User::children()` |
| Child request | A guardian's request to register or link a child, pending until leadership reviews it. | `ChildRegistration` |
| Invitation | An expiring, single-use link that creates or connects an account (parent or student). | `PortalInvitation`, `PortalInvitationService` |
| School workspace | The signed-in area at `/schools/{school}` for school roles. | `WorkspaceController`, `layouts/school` |
| Family portal / Family space | The guardian area at `/family`. Closed to every school role. | `FamilyController`, `User::usesFamilyPortal()` |
| Platform admin | The one account matching `PLATFORM_ADMIN_EMAIL`. Runs `/internal`, sees no private data. | `User::isPlatformAdmin()`, `EnsurePlatformAdmin` |
| Showcase | Fictional demo schools and accounts (`.invalid` emails). Never real data. | `ShowcaseSeeder`, `SHOWCASE_ENABLED` |
| Prototype | The original plain-PHP app kept on `main`. | `prototype:import` |
| Demo branch | `my-sports-app-demo-1`, the integration branch all task PRs target. | `CONTRIBUTING.md` |
| Audit | A row recording who changed school-relevant access or data. | `OnboardingAudit`, `platform_activities` |
