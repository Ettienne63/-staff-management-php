# Parent and student onboarding

> Parent-first: a school invites the parent, the parent requests a child link, leadership verifies it, then a student with their own email may be invited. Records never need a login; names or shared emails never link identities.

**Code:** `app/PortalInvitationService.php`, `InvitationController`, `FamilyController`, `SchoolOnboardingController`, models `PortalInvitation`, `ChildRegistration`, `Student`, `OnboardingAudit`. Tests: `tests/Feature/FamilyOnboardingTest.php`.  
**Last checked:** 2026-11-24 against `6f02660`

## Confirmed

The page foundations now include parent-first onboarding and verified child profiles. Sports workflows retain honest empty states and no fictional performance data is presented.

Separate student records from account identities. An email's owner does not define who the student is.
Parent-first onboarding is the confirmed direction: the parent receives the initial login and submits a request to register or link a child. The school must verify the child and guardian relationship before granting access to child records. An existing school student record must be linked rather than duplicated; submitting a request alone grants no access.
Student activation is implemented: after school verification, leadership or an actively verified guardian with access to that school may invite a student with their own unused email to create a separate account linked to that same child record. Guardians send or replace invitations from the verified child profile. Acceptance automatically grants student membership at the child record's school, and rechecks the issuer's current school access and guardian relationship. Parent invitation acceptance automatically grants guardian membership at the inviting school. The parent and student keep separate logins and permissions. Student login without a personal email and recovery flows remain to be decided; the child record does not require a login.
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

Students receive signup invitations and password reset support.
School-assisted activation for families without email remains a proposal. The school verifies guardian links before granting access.

## Open or proposed

Guardian portal direction (progress, family events): [PRODUCT_PLAN → Proposed guardian portal direction](../PRODUCT_PLAN.md#proposed-guardian-portal-direction). Password reset delivery and access for families without email are still open.
