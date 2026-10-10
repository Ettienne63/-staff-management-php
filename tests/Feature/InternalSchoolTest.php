<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InternalSchoolTest extends TestCase
{
    use RefreshDatabase;

    public function test_internal_routes_require_authentication_and_designated_admin(): void
    {
        $this->get(route('internal.schools.index'))->assertRedirect(route('login'));
        $user = User::factory()->create();
        $this->actingAs($user);
        foreach (['internal.schools.index', 'internal.schools.create'] as $route) {
            $this->get(route($route))->assertForbidden();
        }
        $this->post(route('internal.schools.store'), ['name' => 'Unauthorized'])->assertForbidden();
        $this->assertDatabaseCount('schools', 0);
        config(['app.platform_admin_email' => $user->email]);
        $this->get(route('internal.schools.index'))->assertOk();
        $this->get(route('internal.schools.create'))->assertOk();
        config(['app.platform_admin_email' => null]);
        $this->get(route('internal.schools.index'))->assertForbidden();
    }

    public function test_admin_creates_school_and_assigns_only_the_selected_principal(): void
    {
        $admin = User::factory()->create();
        $principal = User::factory()->create();
        config(['app.platform_admin_email' => $admin->email]);
        $this->actingAs($admin)->post(route('internal.schools.store'), [
            'name' => 'Synthetic school', 'principal_email' => $principal->email,
            'role' => 'coach',
        ])->assertRedirect(route('internal.schools.index'));
        $school = School::sole();
        $this->assertDatabaseHas('school_user', ['school_id' => $school->id, 'user_id' => $principal->id, 'role' => 'principal']);
        $this->assertDatabaseMissing('school_user', ['user_id' => $admin->id]);
        $this->get(route('staff.index', $school))->assertNotFound();
    }

    public function test_invalid_school_creation_does_not_write_school_or_membership(): void
    {
        $admin = User::factory()->create();
        config(['app.platform_admin_email' => $admin->email]);
        $this->actingAs($admin)->post(route('internal.schools.store'), [
            'name' => 'Synthetic school', 'principal_email' => 'missing@example.test',
        ])->assertSessionHasErrors('principal_email');
        $this->assertDatabaseCount('schools', 0);
        $this->assertDatabaseCount('school_user', 0);
        $school = School::factory()->create();
        $this->post(route('internal.schools.store'), [
            'name' => $school->name, 'principal_email' => $admin->email,
        ])->assertSessionHasErrors('name');
        $this->assertDatabaseCount('schools', 1);
    }

    public function test_directory_escapes_school_names(): void
    {
        $admin = User::factory()->create();
        config(['app.platform_admin_email' => $admin->email]);
        School::factory()->create(['name' => '<script>alert(1)</script>']);
        $this->actingAs($admin)->get(route('internal.schools.index'))
            ->assertSee('&lt;script&gt;', false)->assertDontSee('<script>alert(1)</script>', false);
    }
}
