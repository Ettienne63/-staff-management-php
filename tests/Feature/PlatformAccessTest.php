<?php

namespace Tests\Feature;

use App\Models\PortalInvitation;
use App\Models\School;
use App\Models\StaffMember;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PlatformAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_cannot_use_school_memberships_or_guardian_links_to_access_private_data(): void
    {
        $admin = User::factory()->create();
        config(['app.platform_admin_email' => $admin->email]);
        $school = School::factory()->create();
        $staff = StaffMember::factory()->for($school)->create();
        $student = Student::factory()->for($school)->create();
        $admin->schools()->attach($school, ['role' => 'principal']);
        $admin->children()->attach($student, ['verified_at' => now()]);
        $token = Str::random(64);
        PortalInvitation::factory()->for($school)->create(['token_hash' => hash('sha256', $token)]);
        $this->actingAs($admin)->withSession(['school_id' => $school->id]);
        foreach ([route('schools.workspace', $school), route('staff.index', $school), route('staff.edit', [$school, $staff]),
            route('schools.section', [$school, 'students']), route('schools.section', [$school, 'accounts']),
            route('schools.section', [$school, 'progress']), route('family.dashboard'),
            route('family.children.show', $student), route('invitations.show', $token), '/admin/staff'] as $url) {
            $this->get($url)->assertNotFound();
        }
        $this->post(route('schools.select', $school))->assertNotFound();
        $this->post(route('staff.store', $school), [])->assertNotFound();
        $this->put(route('staff.update', [$school, $staff]), [])->assertNotFound();
        $this->delete(route('staff.destroy', [$school, $staff]))->assertNotFound();
        $this->post(route('onboarding.guardians.invite', $school), [])->assertNotFound();
        $this->post(route('family.requests.store'), [])->assertNotFound();
        $this->post(route('family.children.invite', $student), [])->assertNotFound();
        $this->post(route('invitations.accept', $token), [])->assertNotFound();
        $this->assertDatabaseHas('staff_members', ['id' => $staff->id]);
        $this->get(route('internal.schools.index'))->assertOk()->assertSee($school->name)
            ->assertDontSee($staff->email)->assertDontSee($student->first_name)->assertDontSee('Family space')->assertDontSee('My schools');
    }

    public function test_admin_login_discards_private_intended_destination_and_cannot_assign_itself_a_school(): void
    {
        $admin = User::factory()->create();
        config(['app.platform_admin_email' => $admin->email]);
        $this->withSession(['url.intended' => route('family.dashboard')])
            ->post(route('login.store'), ['email' => $admin->email, 'password' => 'password'])
            ->assertRedirect(route('internal.schools.index'));
        $this->post(route('internal.schools.store'), ['name' => 'Self assigned school', 'principal_email' => $admin->email])
            ->assertSessionHasErrors('principal_email');
        $this->assertDatabaseCount('schools', 0);
    }

    public function test_hr_only_sees_one_registered_school_and_cannot_access_family_space(): void
    {
        $hr = User::factory()->create();
        $first = School::factory()->create();
        $second = School::factory()->create();
        $hr->schools()->attach($first, ['role' => 'hr']);
        $hr->schools()->attach($second, ['role' => 'coach']);
        $staff = StaffMember::factory()->for($second)->create();
        $this->actingAs($hr)->withSession(['school_id' => $second->id])->get(route('dashboard'))
            ->assertOk()->assertSee($first->name)->assertDontSee($second->name)->assertDontSee('Family space');
        $this->get(route('schools.workspace', $first))->assertOk()->assertDontSee('Family space');
        $this->get(route('staff.index', $first))->assertOk();
        $this->get('/admin/staff')->assertRedirect(route('staff.index', $first));
        $this->get(route('schools.workspace', $second))->assertNotFound();
        $this->get(route('staff.index', $second))->assertNotFound();
        $this->post(route('schools.select', $second))->assertNotFound();
        $this->delete(route('staff.destroy', [$second, $staff]))->assertNotFound();
        $this->get(route('family.dashboard'))->assertNotFound();
        $this->get(route('family.dashboard', ['section' => 'events']))->assertNotFound();
        $this->post(route('family.requests.store'), [])->assertNotFound();
        $hr->schools()->updateExistingPivot($second->id, ['role' => 'hr']);
        $this->get(route('schools.workspace', $second))->assertNotFound();
        $hr->schools()->detach($first);
        $this->get(route('schools.workspace', $first))->assertNotFound();
        $this->get(route('schools.workspace', $second))->assertOk();
    }
}
