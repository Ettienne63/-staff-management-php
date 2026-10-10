<?php

namespace Tests\Feature;

use App\Models\ChildRegistration;
use App\Models\PortalInvitation;
use App\Models\School;
use App\Models\Student;
use App\Models\User;
use App\Notifications\PortalInvitationNotification;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Tests\TestCase;

class FamilyOnboardingTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();
    }

    private function member(School $school, string $role): User
    {
        $user = User::factory()->create();
        $user->schools()->attach($school, ['role' => $role]);

        return $user;
    }

    private function latestToken(): string
    {
        Notification::assertSentOnDemand(PortalInvitationNotification::class);
        $notification = Notification::sent(new AnonymousNotifiable, PortalInvitationNotification::class)->last();

        return basename(parse_url($notification->url, PHP_URL_PATH));
    }

    private function signup(string $name): array
    {
        return ['name' => $name, 'password' => 'SeparatePassword123', 'password_confirmation' => 'SeparatePassword123'];
    }

    private function childData(School $school): array
    {
        $student = Student::factory()->make(['school_id' => $school->id]);

        return $student->only(['school_id', 'first_name', 'last_name', 'grade'])
            + ['date_of_birth' => $student->date_of_birth->format('Y-m-d'), 'relationship' => 'parent'];
    }

    public function test_parent_first_onboarding_activates_a_separate_student_portal(): void
    {
        $school = School::factory()->create();
        $principal = $this->member($school, 'principal');
        $this->actingAs($principal)->post(route('onboarding.guardians.invite', $school), ['email' => 'parent@example.test'])
            ->assertRedirect(route('schools.section', [$school, 'accounts']));
        $parentToken = $this->latestToken();
        $this->assertDatabaseHas('portal_invitations', ['token_hash' => hash('sha256', $parentToken)]);
        $this->assertDatabaseMissing('portal_invitations', ['token_hash' => $parentToken]);
        $this->post(route('logout'));
        $this->get(route('invitations.show', $parentToken))->assertOk();
        $this->post(route('invitations.accept', $parentToken), $this->signup('Test Parent'))->assertRedirect(route('family.dashboard'));
        $parent = User::where('email', 'parent@example.test')->firstOrFail();
        $parentPassword = $parent->password;
        $this->assertAuthenticatedAs($parent);
        $this->get(route('family.dashboard'))->assertOk()->assertDontSee('My schools');
        $this->get(route('schools.workspace', $school))->assertNotFound();

        $this->post(route('family.requests.store'), $this->childData($school) + ['student_email' => 'child@example.test'])
            ->assertRedirect(route('family.dashboard'));
        $this->assertDatabaseCount('students', 0);
        $registration = ChildRegistration::sole();
        $this->actingAs($principal)->get(route('schools.section', [$school, 'students']))
            ->assertOk()->assertSee($registration->first_name)->assertSee($parent->email);
        $this->actingAs($principal)->post(route('onboarding.requests.review', [$school, $registration->id]), ['decision' => 'approve', 'email' => 'child@example.test'])
            ->assertRedirect(route('schools.section', [$school, 'students']));
        $student = Student::sole();
        $studentToken = $this->latestToken();
        $this->get(route('schools.section', [$school, 'students']))->assertOk()->assertSee('Test Parent');
        $this->actingAs($parent)->get(route('family.children.show', $student))->assertOk()->assertSee($student->first_name);
        $this->post(route('logout'));
        $this->post(route('invitations.accept', $studentToken), $this->signup('Test Student'))
            ->assertRedirect(route('schools.workspace', $school));
        $studentUser = User::where('email', 'child@example.test')->firstOrFail();
        $this->assertAuthenticatedAs($studentUser);
        $this->assertSame($studentUser->id, $student->fresh()->user_id);
        $this->assertNotSame($parent->id, $studentUser->id);
        $this->assertSame($parentPassword, $parent->fresh()->password);
        $this->assertTrue(Hash::check('SeparatePassword123', $studentUser->password));
        $this->assertCount(1, $studentUser->schools);
        $this->get(route('schools.workspace', $school))->assertOk()->assertSee($student->first_name);
        $this->get(route('staff.index', $school))->assertForbidden();
        $this->post(route('invitations.accept', $studentToken))->assertGone();
        $this->post(route('logout'));
        $this->post(route('login.store'), ['email' => $studentUser->email, 'password' => 'SeparatePassword123'])
            ->assertRedirect(route('schools.workspace', $school));
        $this->assertDatabaseHas('onboarding_audits', ['action' => 'child.approved', 'student_id' => $student->id, 'actor_id' => $principal->id]);
    }

    public function test_parent_must_be_invited_and_cannot_verify_their_own_request(): void
    {
        $school = School::factory()->create();
        $parent = $this->member($school, 'guardian');
        $other = School::factory()->create();
        $this->actingAs($parent)->post(route('family.requests.store'), $this->childData($other))->assertNotFound();
        $data = $this->childData($school);
        $this->post(route('family.requests.store'), $data)->assertRedirect();
        $this->post(route('family.requests.store'), $data)->assertSessionHasErrors('first_name');
        $registration = ChildRegistration::sole();
        $this->post(route('onboarding.requests.review', [$school, $registration->id]), ['decision' => 'approve'])->assertNotFound();
        $this->assertDatabaseCount('students', 0);
        $unlinked = Student::factory()->create(['school_id' => $school->id]);
        $this->get(route('family.children.show', $unlinked))->assertNotFound();
    }

    public function test_school_links_an_existing_student_without_merging_or_duplicating_records(): void
    {
        $school = School::factory()->create();
        $principal = $this->member($school, 'principal');
        $student = Student::factory()->create(['school_id' => $school->id]);
        $parent = $this->member($school, 'guardian');
        $registration = ChildRegistration::factory()->create($student->only(['school_id', 'first_name', 'last_name', 'date_of_birth', 'grade']) + ['guardian_user_id' => $parent->id]);
        $url = route('onboarding.requests.review', [$school, $registration->id]);
        $this->actingAs($principal)->post($url, ['decision' => 'approve'])->assertSessionHasErrors('student_id');
        $this->post($url, ['decision' => 'approve', 'student_id' => $student->id])->assertRedirect();
        $this->assertDatabaseCount('students', 1);
        $this->assertDatabaseHas('guardian_student', ['user_id' => $parent->id, 'student_id' => $student->id, 'revoked_at' => null]);
        $this->post($url, ['decision' => 'approve', 'student_id' => $student->id])->assertSessionHasErrors('decision');
    }

    public function test_guardian_has_multiple_distinct_children_and_revocation_takes_effect_immediately(): void
    {
        $firstSchool = School::factory()->create();
        $secondSchool = School::factory()->create();
        $principal = $this->member($firstSchool, 'principal');
        $parent = $this->member($firstSchool, 'guardian');
        $parent->schools()->attach($secondSchool, ['role' => 'guardian']);
        $first = Student::factory()->create(['school_id' => $firstSchool->id]);
        $second = Student::factory()->create(['school_id' => $secondSchool->id]);
        $unlinked = Student::factory()->create(['school_id' => $firstSchool->id]);
        foreach ([$first, $second] as $student) {
            $student->guardians()->attach($parent, ['verified_by' => $principal->id, 'verified_at' => now()]);
        }
        $this->actingAs($parent)->get(route('family.dashboard'))->assertSee($first->first_name.' '.$first->last_name)
            ->assertSee($second->first_name.' '.$second->last_name)->assertDontSee($unlinked->first_name.' '.$unlinked->last_name);
        $this->get(route('family.children.show', $second))->assertOk();
        $this->get(route('family.children.show', $unlinked))->assertNotFound();
        $this->actingAs($principal)->delete(route('onboarding.guardians.revoke', [$firstSchool, $first->id, $parent->id]))->assertRedirect();
        $this->actingAs($parent)->get(route('family.children.show', $first))->assertNotFound();
        $this->get(route('family.children.show', $second))->assertOk();
        $this->assertDatabaseHas('onboarding_audits', ['action' => 'guardian.revoked', 'student_id' => $first->id]);
    }

    public function test_school_isolation_applies_to_reviews_links_and_student_invitations(): void
    {
        $school = School::factory()->create();
        $other = School::factory()->create();
        $principal = $this->member($school, 'principal');
        $registration = ChildRegistration::factory()->create(['school_id' => $other->id]);
        $student = Student::factory()->create(['school_id' => $other->id]);
        $this->actingAs($principal)->post(route('onboarding.requests.review', [$school, $registration->id]), ['decision' => 'approve'])->assertNotFound();
        $this->post(route('onboarding.students.invite', [$school, $student->id]), ['email' => 'student@example.test'])->assertNotFound();
        $parent = $this->member($school, 'guardian');
        $local = ChildRegistration::factory()->create(['school_id' => $school->id, 'guardian_user_id' => $parent->id]);
        $this->post(route('onboarding.requests.review', [$school, $local->id]), ['decision' => 'approve', 'student_id' => $student->id])->assertNotFound();
        $invitation = PortalInvitation::factory()->create(['school_id' => $other->id]);
        $this->delete(route('onboarding.invitations.revoke', [$school, $invitation->id]))->assertNotFound();
        $this->assertDatabaseCount('guardian_student', 0);
    }

    public function test_hr_can_invite_parents_but_hr_and_coaches_cannot_verify_or_activate_students(): void
    {
        $school = School::factory()->create();
        $registration = ChildRegistration::factory()->create(['school_id' => $school->id]);
        $student = Student::factory()->create(['school_id' => $school->id]);
        $hr = $this->member($school, 'hr');
        $coach = $this->member($school, 'coach');
        $this->actingAs($hr)->post(route('onboarding.guardians.invite', $school), ['email' => 'parent@example.test'])->assertRedirect();
        foreach ([$hr, $coach] as $user) {
            $this->actingAs($user)->post(route('onboarding.requests.review', [$school, $registration->id]), ['decision' => 'approve'])->assertForbidden();
            $this->post(route('onboarding.students.invite', [$school, $student->id]), ['email' => 'student@example.test'])->assertForbidden();
        }
        $this->actingAs($coach)->get(route('schools.section', [$school, 'students']))->assertOk()->assertSee($student->first_name)->assertDontSee($registration->guardian->email);
        $this->actingAs($hr)->get(route('schools.section', [$school, 'students']))->assertForbidden();
        $invitation = PortalInvitation::factory()->create(['school_id' => $school->id, 'student_id' => $student->id, 'type' => 'student']);
        $this->get(route('schools.section', [$school, 'accounts']))->assertOk()->assertSee('parent@example.test')->assertDontSee($invitation->email);
    }

    public function test_existing_parent_acceptance_preserves_password_and_requires_the_invited_account(): void
    {
        $school = School::factory()->create();
        $principal = $this->member($school, 'principal');
        $parent = User::factory()->create();
        $password = $parent->password;
        $this->actingAs($principal)->post(route('onboarding.guardians.invite', $school), ['email' => strtoupper($parent->email)])->assertRedirect();
        $token = $this->latestToken();
        $this->post(route('invitations.accept', $token))->assertSessionHasErrors('email');
        $this->post(route('logout'));
        $this->post(route('invitations.accept', $token), $this->signup('Another Name'))->assertSessionHasErrors('email');
        $this->actingAs($parent)->post(route('invitations.accept', $token))->assertRedirect(route('family.dashboard'));
        $this->assertSame($password, $parent->fresh()->password);
        $this->assertDatabaseHas('school_user', ['user_id' => $parent->id, 'school_id' => $school->id, 'role' => 'guardian']);
    }

    public function test_student_invitation_cannot_reuse_a_parent_account_or_activate_without_verification(): void
    {
        $school = School::factory()->create();
        $principal = $this->member($school, 'principal');
        $parent = $this->member($school, 'guardian');
        $student = Student::factory()->create(['school_id' => $school->id]);
        $url = route('onboarding.students.invite', [$school, $student->id]);
        $this->actingAs($principal)->post($url, ['email' => 'child@example.test'])->assertSessionHasErrors('email');
        $student->guardians()->attach($parent, ['verified_by' => $principal->id, 'verified_at' => now()]);
        $this->post($url, ['email' => $parent->email])->assertSessionHasErrors('email');
        $this->assertDatabaseCount('portal_invitations', 0);
    }

    public function test_expired_replaced_and_revoked_invitation_links_cannot_be_used(): void
    {
        $school = School::factory()->create();
        $principal = $this->member($school, 'principal');
        $url = route('onboarding.guardians.invite', $school);
        $this->actingAs($principal)->post($url, ['email' => 'parent@example.test'])->assertRedirect();
        $first = $this->latestToken();
        $this->post($url, ['email' => 'parent@example.test'])->assertRedirect();
        $second = $this->latestToken();
        $this->get(route('invitations.show', $first))->assertGone();
        $invitation = PortalInvitation::where('token_hash', hash('sha256', $second))->firstOrFail();
        $this->delete(route('onboarding.invitations.revoke', [$school, $invitation->id]))->assertRedirect();
        $this->get(route('invitations.show', $second))->assertGone();
        $expiredToken = Str::random(64);
        PortalInvitation::factory()->create(['school_id' => $school->id, 'created_by' => $principal->id, 'token_hash' => hash('sha256', $expiredToken), 'expires_at' => now()->subSecond()]);
        $this->post(route('logout'));
        $this->post(route('invitations.accept', $expiredToken), $this->signup('Test Parent'))->assertGone();
        $this->get(route('invitations.show', Str::random(64)))->assertNotFound();
        $this->assertDatabaseMissing('users', ['email' => 'parent@example.test']);
    }

    public function test_invitation_cannot_be_accepted_after_issuing_membership_is_revoked(): void
    {
        $school = School::factory()->create();
        $principal = $this->member($school, 'principal');
        $this->actingAs($principal)->post(route('onboarding.guardians.invite', $school), ['email' => 'parent@example.test'])->assertRedirect();
        $token = $this->latestToken();
        $principal->schools()->detach($school);
        $this->post(route('logout'));
        $this->post(route('invitations.accept', $token), $this->signup('Test Parent'))->assertGone();
        $this->assertDatabaseMissing('users', ['email' => 'parent@example.test']);
    }

    public function test_rejection_and_validation_do_not_grant_access(): void
    {
        $school = School::factory()->create();
        $parent = $this->member($school, 'guardian');
        $this->actingAs($parent)->post(route('family.requests.store'), $this->childData($school) + ['student_email' => 'not-an-email'])->assertSessionHasErrors('student_email');
        $invalidGrade = $this->childData($school);
        $invalidGrade['grade'] = 13;
        $this->post(route('family.requests.store'), $invalidGrade)->assertSessionHasErrors('grade');
        $registration = ChildRegistration::factory()->create(['school_id' => $school->id, 'guardian_user_id' => $parent->id]);
        $principal = $this->member($school, 'deputy');
        $this->actingAs($principal)->post(route('onboarding.requests.review', [$school, $registration->id]), ['decision' => 'reject', 'review_note' => 'Please confirm the school enrollment.'])->assertRedirect();
        $this->assertSame('rejected', $registration->fresh()->status);
        $this->assertDatabaseCount('students', 0);
        $this->assertDatabaseCount('guardian_student', 0);
        $this->assertDatabaseHas('onboarding_audits', ['action' => 'child.rejected']);
    }

    public function test_linking_another_parent_preserves_an_activated_student_account(): void
    {
        $school = School::factory()->create();
        $principal = $this->member($school, 'principal');
        $studentUser = $this->member($school, 'student');
        $student = Student::factory()->create(['school_id' => $school->id, 'user_id' => $studentUser->id]);
        $parent = $this->member($school, 'guardian');
        $registration = ChildRegistration::factory()->create(['school_id' => $school->id, 'guardian_user_id' => $parent->id]);
        $password = $studentUser->password;
        $this->actingAs($principal)->post(route('onboarding.requests.review', [$school, $registration->id]),
            ['decision' => 'approve', 'student_id' => $student->id, 'email' => $studentUser->email])->assertRedirect();
        $this->assertSame($studentUser->id, $student->fresh()->user_id);
        $this->assertSame($password, $studentUser->fresh()->password);
        $this->assertDatabaseCount('students', 1);
        $this->assertDatabaseCount('portal_invitations', 0);
        $this->actingAs($parent)->get(route('family.children.show', $student))->assertOk();
    }

    public function test_known_child_cannot_be_registered_in_two_schools(): void
    {
        $first = School::factory()->create();
        $second = School::factory()->create();
        $principal = $this->member($second, 'principal');
        $parent = $this->member($first, 'guardian');
        $parent->schools()->attach($second, ['role' => 'guardian']);
        $student = Student::factory()->create(['school_id' => $first->id]);
        $student->guardians()->attach($parent, ['verified_at' => now()]);
        $data = $student->only(['first_name', 'last_name', 'grade'])
            + ['school_id' => $second->id, 'date_of_birth' => $student->date_of_birth->format('Y-m-d'), 'relationship' => 'parent'];
        $this->actingAs($parent)->post(route('family.requests.store'), $data)->assertSessionHasErrors('school_id');
        $pending = ChildRegistration::factory()->create($data + ['guardian_user_id' => $parent->id]);
        $this->actingAs($principal)->post(route('onboarding.requests.review', [$second, $pending->id]), ['decision' => 'approve'])->assertSessionHasErrors('student_id');
        $this->assertDatabaseCount('students', 1);
        $this->assertSame('pending', $pending->fresh()->status);
    }

    public function test_parent_enrollment_revocation_prevents_pending_approval(): void
    {
        $school = School::factory()->create();
        $principal = $this->member($school, 'principal');
        $parent = $this->member($school, 'guardian');
        $registration = ChildRegistration::factory()->create(['school_id' => $school->id, 'guardian_user_id' => $parent->id]);
        $parent->schools()->detach($school);
        $this->actingAs($principal)->post(route('onboarding.requests.review', [$school, $registration->id]), ['decision' => 'approve'])->assertSessionHasErrors('decision');
        $this->assertDatabaseCount('students', 0);
    }

    public function test_email_collision_after_student_invitation_cannot_merge_accounts(): void
    {
        $school = School::factory()->create();
        $principal = $this->member($school, 'principal');
        $parent = $this->member($school, 'guardian');
        $student = Student::factory()->create(['school_id' => $school->id]);
        $student->guardians()->attach($parent, ['verified_at' => now()]);
        $this->actingAs($principal)->post(route('onboarding.students.invite', [$school, $student->id]), ['email' => 'child@example.test'])->assertRedirect();
        $token = $this->latestToken();
        $collision = User::factory()->create(['email' => 'child@example.test']);
        $password = $collision->password;
        $this->post(route('logout'));
        $this->post(route('invitations.accept', $token), $this->signup('Student'))->assertSessionHasErrors('email');
        $this->assertNull($student->fresh()->user_id);
        $this->assertSame($password, $collision->fresh()->password);
        $this->assertNull(PortalInvitation::sole()->accepted_at);
    }
}
