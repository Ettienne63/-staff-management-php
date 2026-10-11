<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\ShowcaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ShowcasePasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_password_prompt_updates_only_showcase_accounts_and_preserves_memberships(): void
    {
        $other = User::factory()->create();
        $oldHash = $other->password;
        $this->seed(ShowcaseSeeder::class);
        $memberships = DB::table('school_user')->orderBy('user_id')->orderBy('school_id')->get()->toJson();
        $this->artisan('showcase:password')->expectsQuestion('New showcase password', 'Preview-pass-42!')
            ->expectsOutput('Updated 6 showcase login password(s). Other accounts and memberships were preserved.')->assertSuccessful();
        foreach (User::where('email', 'like', '%@showcase.example.invalid')->get() as $user) {
            $this->assertTrue(Hash::check('Preview-pass-42!', $user->password));
            $this->assertNotSame('Preview-pass-42!', $user->password);
        }
        $this->assertSame($oldHash, $other->fresh()->password);
        $this->assertSame($memberships, DB::table('school_user')->orderBy('user_id')->orderBy('school_id')->get()->toJson());
        $this->seed(ShowcaseSeeder::class);
        $this->assertTrue(Hash::check('Preview-pass-42!', User::where('email', 'principal@showcase.example.invalid')->firstOrFail()->password));
    }

    public function test_existing_matching_account_without_showcase_name_keeps_its_password(): void
    {
        $existing = User::factory()->create(['email' => 'coach@showcase.example.invalid']);
        $oldHash = $existing->password;
        $this->seed(ShowcaseSeeder::class);
        $this->artisan('showcase:password')->expectsQuestion('New showcase password', 'Preview-pass-42!')->assertSuccessful();
        $this->assertSame($oldHash, $existing->fresh()->password);
    }

    public function test_hosted_password_changes_require_explicit_demo_enablement_even_with_force(): void
    {
        $this->seed(ShowcaseSeeder::class);
        $principal = User::where('email', 'principal@showcase.example.invalid')->firstOrFail();
        $oldHash = $principal->password;
        $this->app->instance('env', 'production');
        config(['app.showcase_enabled' => false]);
        $this->artisan('showcase:password', ['--force' => true])->assertFailed();
        $this->assertSame($oldHash, $principal->fresh()->password);
        config(['app.showcase_enabled' => true, 'app.debug' => false]);
        $this->artisan('showcase:password', ['--force' => true])->expectsQuestion('New showcase password', 'Preview-pass-42!')->assertSuccessful();
        $this->assertTrue(Hash::check('Preview-pass-42!', $principal->fresh()->password));
        $this->assertFalse(config('app.debug'));
    }

    public function test_invalid_password_or_missing_fixtures_returns_failure_without_changes(): void
    {
        $this->artisan('showcase:password')->expectsQuestion('New showcase password', 'Preview-pass-42!')->assertFailed();
        $this->assertDatabaseCount('users', 0);
        $this->seed(ShowcaseSeeder::class);
        $principal = User::where('email', 'principal@showcase.example.invalid')->firstOrFail();
        $oldHash = $principal->password;
        foreach (['short', str_repeat('x', 73)] as $password) {
            $this->artisan('showcase:password')->expectsQuestion('New showcase password', $password)->assertFailed();
            $this->assertSame($oldHash, $principal->fresh()->password);
        }
    }
}
