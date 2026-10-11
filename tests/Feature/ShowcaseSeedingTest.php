<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\User;
use Database\Seeders\ShowcaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use RuntimeException;
use Tests\TestCase;

class ShowcaseSeedingTest extends TestCase
{
    use RefreshDatabase;

    public function test_showcase_covers_current_roles_and_preserves_existing_data_and_passwords(): void
    {
        $existingUser = User::factory()->create();
        $existingSchool = School::factory()->create();
        $existingUser->schools()->attach($existingSchool, ['role' => 'principal']);

        $this->seed(ShowcaseSeeder::class);
        $passwordHash = User::where('email', 'coach@showcase.example.invalid')->firstOrFail()->password;
        $this->seed(ShowcaseSeeder::class);

        $this->assertDatabaseCount('users', 7);
        $this->assertDatabaseCount('schools', 3);
        $this->assertDatabaseCount('school_user', 7);
        $this->assertDatabaseCount('staff_members', 12);
        $this->assertSame($passwordHash, User::where('email', 'coach@showcase.example.invalid')->firstOrFail()->password);
        $this->assertFalse(Hash::check('password', $passwordHash));
        $this->assertDatabaseHas('school_user', ['school_id' => $existingSchool->id, 'user_id' => $existingUser->id, 'role' => 'principal']);

        foreach (User::SchoolRoles as $role) {
            $user = User::where('email', $role.'@showcase.example.invalid')->firstOrFail();
            $expectedMembershipCount = $role === 'coach' ? 2 : 1;
            $this->assertCount($expectedMembershipCount, $user->schools);
            $this->assertSame(array_fill(0, $expectedMembershipCount, $role), $user->schools->pluck('pivot.role')->all());
            foreach ($user->schools as $school) {
                $this->actingAs($user)->get(route('schools.workspace', $school))->assertOk();
            }
        }

        $principal = User::where('email', 'principal@showcase.example.invalid')->firstOrFail();
        $schools = School::whereIn('name', ['Showcase Oakfield School', 'Showcase Riverbend School'])->get();
        $this->actingAs($principal)->get(route('dashboard'))
            ->assertSee('Showcase Oakfield School')->assertDontSee('Showcase Riverbend School');
        $this->get(route('schools.workspace', $schools->firstWhere('name', 'Showcase Riverbend School')))
            ->assertNotFound();

        $coach = User::where('email', 'coach@showcase.example.invalid')->firstOrFail();
        $this->actingAs($coach)->get(route('dashboard'))
            ->assertSee('Showcase Oakfield School')->assertSee('Showcase Riverbend School');

        $guardian = User::where('email', 'guardian@showcase.example.invalid')->firstOrFail();
        $this->assertCount(0, $guardian->schools);
        $this->actingAs($guardian)->get(route('family.dashboard'))->assertOk();
    }

    public function test_showcase_does_not_change_an_existing_matching_account(): void
    {
        $user = User::factory()->create(['email' => 'coach@showcase.example.invalid']);
        $password = $user->password;
        $this->seed(ShowcaseSeeder::class);

        $this->assertSame($password, $user->fresh()->password);
        $this->assertCount(0, $user->fresh()->schools);
    }

    public function test_showcase_is_blocked_outside_local_and_testing(): void
    {
        $this->app->instance('env', 'production');

        try {
            $this->artisan('db:seed', ['--class' => ShowcaseSeeder::class, '--force' => true]);
            $this->fail('Production seeding should be rejected.');
        } catch (RuntimeException $exception) {
            $this->assertSame('Showcase data is available only in local and testing environments.', $exception->getMessage());
        }

        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('schools', 0);
    }
}
