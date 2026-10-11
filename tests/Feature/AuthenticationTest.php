<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_from_the_dashboard(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_valid_login_authenticates_and_logout_ends_the_session(): void
    {
        $user = User::factory()->create();
        $this->post(route('login.store'), ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect(route('family.dashboard'));
        $this->assertAuthenticatedAs($user);
        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_family_account_cannot_land_on_the_school_dashboard_after_login(): void
    {
        $user = User::factory()->create();
        $this->get(route('dashboard'))->assertRedirect(route('login'));
        $this->post(route('login.store'), ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect(route('dashboard'));
        $this->get(route('dashboard'))->assertRedirect(route('family.dashboard'));
        $this->get(route('family.dashboard'))->assertOk()->assertDontSee('My schools');
    }

    public function test_school_member_login_keeps_the_school_dashboard(): void
    {
        $user = User::factory()->create();
        $user->schools()->attach(School::factory()->create(), ['role' => 'student']);

        $this->post(route('login.store'), ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect(route('dashboard'));
        $this->get(route('dashboard'))->assertOk()->assertSee('My schools');
    }

    public function test_platform_admin_without_school_memberships_keeps_the_admin_entry(): void
    {
        $user = User::factory()->create();
        config(['app.platform_admin_email' => $user->email]);

        $this->post(route('login.store'), ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect(route('internal.schools.index'));
        $this->get(route('dashboard'))->assertRedirect(route('internal.schools.index'));
        $this->get(route('internal.schools.index'))->assertOk()->assertSee('Platform admin')->assertDontSee('Family space')->assertDontSee('My schools');
    }

    public function test_invalid_login_does_not_authenticate(): void
    {
        $user = User::factory()->create();
        $this->post(route('login.store'), ['email' => $user->email, 'password' => 'incorrect'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_login_attempts_are_rate_limited(): void
    {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post(route('login.store'), ['email' => 'unknown@example.test', 'password' => 'incorrect']);
        }
        $this->post(route('login.store'), ['email' => 'unknown@example.test', 'password' => 'incorrect'])
            ->assertStatus(429);
    }
}
