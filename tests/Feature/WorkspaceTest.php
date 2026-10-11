<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\StaffMember;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class WorkspaceTest extends TestCase
{
    use RefreshDatabase;

    public static function workspaceRoles(): array
    {
        return [
            'principal' => ['principal', 'Principal view', 'schools.leadership.dashboard'],
            'vice' => ['deputy', 'Vice principal view', 'schools.leadership.dashboard'],
            'hr' => ['hr', 'HR view', 'schools.hr.dashboard'],
            'coach' => ['coach', 'Coach view', 'schools.coach.dashboard'],
            'student' => ['student', 'Student view', 'student.dashboard'],
        ];
    }

    #[DataProvider('workspaceRoles')]
    public function test_workspace_uses_current_school_role_and_enforces_isolation(string $role, string $label, string $view): void
    {
        $school = School::factory()->create();
        $other = School::factory()->create();
        $user = User::factory()->create();
        $user->schools()->attach($school, ['role' => $role]);
        $this->actingAs($user)->get(route('dashboard'))->assertSee($school->name)->assertDontSee($other->name);
        $this->get(route('schools.workspace', $school))->assertOk()->assertViewIs($view)->assertSee($label);
        $this->post(route('schools.select', $school))->assertRedirect(route('schools.workspace', $school));
        foreach (['training', 'progress'] as $section) {
            $response = $this->get(route('schools.section', [$school, $section]));
            if ($role === 'hr') {
                $response->assertForbidden();
            } else {
                $response->assertOk()->assertViewIs(($role === 'student' ? 'student.' : 'schools.').$section.'.index');
                if ($role === 'student') {
                    $response->assertDontSee('Family space')->assertDontSee('My schools')->assertDontSee('Children&#8217;s progress', false);
                }
            }
        }
        $this->withSession(['school_id' => $other->id])->get(route('schools.workspace', $other))->assertNotFound();
        $this->get(route('schools.section', [$other, 'training']))->assertNotFound();
        $user->schools()->detach($school);
        $this->get(route('schools.workspace', $school))->assertNotFound();
        $this->get(route('schools.section', [$school, 'progress']))->assertNotFound();
    }

    public function test_student_cannot_access_staff_roster_or_account_administration(): void
    {
        $school = School::factory()->create();
        $user = User::factory()->create();
        $user->schools()->attach($school, ['role' => 'student']);
        $staff = StaffMember::factory()->for($school)->create();
        $this->actingAs($user)->get(route('schools.workspace', $school))
            ->assertDontSee($staff->email)->assertDontSee('staff records')->assertDontSee('Family space')->assertDontSee('My schools');
        $child = Student::factory()->for($school)->create();
        $user->children()->attach($child, ['verified_at' => now()]);
        $this->get(route('family.dashboard'))->assertNotFound();
        $this->get(route('family.dashboard', ['section' => 'events']))->assertNotFound();
        $this->get(route('family.children.show', $child))->assertNotFound();
        $this->post(route('family.requests.store'), [])->assertNotFound();
        $this->post(route('family.children.invite', $child), [])->assertNotFound();
        $this->get(route('staff.index', $school))->assertForbidden();
        $this->get(route('staff.create', $school))->assertForbidden();
        $this->get(route('staff.edit', [$school, $staff->id]))->assertForbidden();
        $this->post(route('staff.store', $school), [])->assertForbidden();
        $this->put(route('staff.update', [$school, $staff->id]), [])->assertForbidden();
        $this->delete(route('staff.destroy', [$school, $staff->id]))->assertForbidden();
        $this->get(route('schools.section', [$school, 'students']))->assertForbidden();
        $this->get(route('schools.section', [$school, 'accounts']))->assertForbidden();
        $this->assertDatabaseHas('staff_members', ['id' => $staff->id]);
    }

    public function test_hr_can_manage_staff_but_has_no_platform_access(): void
    {
        $school = School::factory()->create();
        $user = User::factory()->create();
        $user->schools()->attach($school, ['role' => 'hr']);
        $this->actingAs($user)->get(route('schools.section', [$school, 'accounts']))->assertOk();
        $this->get(route('schools.workspace', $school))->assertViewIs('schools.hr.dashboard')
            ->assertDontSee('Student roster')->assertDontSee('Training planner');
        $this->get(route('schools.section', [$school, 'students']))->assertForbidden();
        $this->get(route('staff.create', $school))->assertOk();
        $fields = StaffMember::factory()->make()->only(['first_name', 'last_name', 'email', 'department', 'position']);
        $this->post(route('staff.store', $school), $fields)->assertRedirect(route('staff.index', $school));
        $staff = $school->staffMembers()->sole();
        $this->put(route('staff.update', [$school, $staff->id]), array_replace($fields, ['position' => 'Head coach']))
            ->assertRedirect(route('staff.index', $school));
        $this->assertDatabaseHas('staff_members', ['id' => $staff->id, 'position' => 'Head coach']);
        $this->delete(route('staff.destroy', [$school, $staff->id]))->assertRedirect(route('staff.index', $school));
        $this->assertDatabaseMissing('staff_members', ['id' => $staff->id]);
        $this->get(route('internal.schools.index'))->assertForbidden();
    }

    public function test_role_change_removes_management_access_on_next_request(): void
    {
        $school = School::factory()->create();
        $user = User::factory()->create();
        $user->schools()->attach($school, ['role' => 'hr']);
        $this->actingAs($user)->get(route('schools.section', [$school, 'accounts']))->assertOk();
        $user->schools()->updateExistingPivot($school->id, ['role' => 'coach']);
        $this->get(route('schools.section', [$school, 'accounts']))->assertForbidden();
        $this->get(route('staff.create', $school))->assertForbidden();
        $this->get(route('schools.workspace', $school))->assertSee('Coach view');
    }

    public function test_workspace_requires_login_and_escapes_school_name(): void
    {
        $school = School::factory()->create(['name' => '<script>alert(1)</script>']);
        $this->get(route('schools.workspace', $school))->assertRedirect(route('login'));
        $user = User::factory()->create();
        $user->schools()->attach($school, ['role' => 'coach']);
        $this->actingAs($user)->get(route('schools.workspace', $school))
            ->assertSee('&lt;script&gt;', false)->assertDontSee('<script>alert(1)</script>', false);
        $this->get('/admin/dashboard')->assertRedirect(route('dashboard'));
        $this->get(route('schools.section', [$school, 'unknown']))->assertNotFound();
    }

    public function test_family_foundation_requires_login_and_exposes_no_child_records(): void
    {
        $this->get(route('family.dashboard'))->assertRedirect(route('login'));
        $eventsUrl = route('family.dashboard', ['section' => 'events']);
        $this->get($eventsUrl)->assertRedirect(route('login'));
        $user = User::factory()->create();
        $school = School::factory()->create();
        $this->actingAs($user)->get(route('family.dashboard'))->assertOk()
            ->assertViewIs('guardian.dashboard')->assertSee('No verified children yet.')
            ->assertDontSee('Keep family sports events in one place.')->assertDontSee($school->name);
        $this->get($eventsUrl)->assertOk()->assertViewIs('guardian.dashboard')
            ->assertSee('Keep family sports events in one place.')
            ->assertDontSee('No verified children yet.')->assertDontSee($school->name);
        $this->get('/family/unknown')->assertNotFound();

        foreach (['principal', 'deputy', 'coach'] as $role) {
            $leader = User::factory()->create();
            $leader->schools()->attach($school, ['role' => $role]);
            $this->actingAs($leader)->get(route('dashboard'))->assertDontSee('Family space');
            $this->get(route('family.dashboard'))->assertNotFound();
            $this->get($eventsUrl)->assertNotFound();
        }
    }

    public function test_student_is_limited_to_one_school_even_with_multiple_memberships(): void
    {
        $first = School::factory()->create();
        $second = School::factory()->create();
        $student = User::factory()->create();
        $student->schools()->attach($first, ['role' => 'student']);
        $student->schools()->attach($second, ['role' => 'student']);

        $this->actingAs($student)->get(route('dashboard'))
            ->assertSee($first->name)->assertDontSee($second->name);
        $this->get(route('schools.workspace', $first))->assertOk();
        $this->get(route('schools.workspace', $second))->assertNotFound();
    }
}
