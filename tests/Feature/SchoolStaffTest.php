<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\StaffMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SchoolStaffTest extends TestCase
{
    use RefreshDatabase;

    private function member(School $school, string $role = 'principal'): User
    {
        $user = User::factory()->create();
        $user->schools()->attach($school, ['role' => $role]);

        return $user;
    }

    private function fields(): array
    {
        return ['first_name' => 'Test', 'last_name' => 'Coach', 'email' => 'coach@example.test',
            'department' => 'Sport', 'position' => 'Coach'];
    }

    public function test_school_members_see_only_their_school_records(): void
    {
        $school = School::factory()->create();
        $other = School::factory()->create();
        $visible = StaffMember::factory()->for($school)->create();
        $hidden = StaffMember::factory()->for($other)->create();
        $user = $this->member($school, 'coach');
        $this->actingAs($user)->get(route('staff.index', $school))
            ->assertOk()->assertSee($visible->email)->assertDontSee($hidden->email);
        $this->get(route('staff.index', $other))->assertNotFound();
        $this->post(route('schools.select', $other))->assertNotFound();
        $this->get(route('staff.edit', [$school, $hidden->id]))->assertForbidden();
    }

    public function test_authorized_multi_school_access_and_revocation(): void
    {
        $first = School::factory()->create();
        $second = School::factory()->create();
        $user = $this->member($first, 'coach');
        $user->schools()->attach($second, ['role' => 'coach']);
        $this->actingAs($user)->get(route('dashboard'))->assertSee($first->name)->assertSee($second->name);
        $this->post(route('schools.select', $second))->assertRedirect(route('schools.workspace', $second))
            ->assertSessionHas('school_id', $second->id);
        $this->get(route('staff.index', $second))->assertOk();
        $user->schools()->detach($second);
        $this->get(route('staff.index', $second))->assertNotFound();
    }

    public function test_principal_can_create_update_and_delete_staff(): void
    {
        $school = School::factory()->create();
        $user = $this->member($school);
        $this->actingAs($user)->post(route('staff.store', $school), $this->fields() + ['school_id' => 999])
            ->assertRedirect(route('staff.index', $school));
        $staff = $school->staffMembers()->sole();
        $this->assertSame($school->id, $staff->school_id);
        $this->put(route('staff.update', [$school, $staff->id]), array_replace($this->fields(), ['position' => 'Head coach']))
            ->assertRedirect(route('staff.index', $school));
        $this->assertSame('Head coach', $staff->fresh()->position);
        $this->delete(route('staff.destroy', [$school, $staff->id]))->assertRedirect(route('staff.index', $school));
        $this->assertDatabaseMissing('staff_members', ['id' => $staff->id]);
    }

    public function test_cross_school_staff_ids_cannot_be_read_updated_or_deleted(): void
    {
        $school = School::factory()->create();
        $other = School::factory()->create();
        $staff = StaffMember::factory()->for($other)->create();
        $this->actingAs($this->member($school))->get(route('staff.edit', [$school, $staff->id]))->assertNotFound();
        $this->put(route('staff.update', [$school, $staff->id]), $this->fields())->assertNotFound();
        $this->delete(route('staff.destroy', [$school, $staff->id]))->assertNotFound();
        $this->assertDatabaseHas('staff_members', ['id' => $staff->id, 'school_id' => $other->id]);
    }

    public function test_coaches_and_students_cannot_manage_staff(): void
    {
        $school = School::factory()->create();
        $staff = StaffMember::factory()->for($school)->create();
        $coach = $this->member($school, 'coach');
        $this->actingAs($coach)->post(route('staff.store', $school), $this->fields())->assertForbidden();
        $this->delete(route('staff.destroy', [$school, $staff->id]))->assertForbidden();
        $student = $this->member($school, 'student');
        $this->actingAs($student)->get(route('staff.index', $school))->assertForbidden();
    }

    public function test_staff_validation_and_email_uniqueness_are_school_scoped(): void
    {
        $school = School::factory()->create();
        $other = School::factory()->create();
        StaffMember::factory()->for($other)->create(['email' => $this->fields()['email']]);
        $this->actingAs($this->member($school))->post(route('staff.store', $school), $this->fields())->assertRedirect();
        $this->post(route('staff.store', $school), $this->fields())->assertSessionHasErrors('email');
        $this->post(route('staff.store', $school), ['email' => 'invalid'])->assertSessionHasErrors(['email', 'first_name']);
        $this->assertDatabaseCount('staff_members', 2);
    }

    public function test_delete_is_not_a_get_action(): void
    {
        $school = School::factory()->create();
        $staff = StaffMember::factory()->for($school)->create();
        $this->actingAs($this->member($school))->get(route('staff.destroy', [$school, $staff->id]))->assertStatus(405);
        $this->assertDatabaseHas('staff_members', ['id' => $staff->id]);
    }
}
