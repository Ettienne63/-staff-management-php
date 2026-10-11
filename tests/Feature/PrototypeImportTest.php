<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\StaffMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PDO;
use Tests\TestCase;

class PrototypeImportTest extends TestCase
{
    use RefreshDatabase;

    private ?string $sourceConfig = null;

    private function source(bool $invalidStaff = false): string
    {
        $source = new PDO('sqlite::memory:');
        $source->exec('CREATE TABLE users (email TEXT, password TEXT)');
        $source->exec('CREATE TABLE staff_members (first_name TEXT, last_name TEXT, email TEXT, department TEXT, position TEXT)');
        $source->prepare('INSERT INTO users VALUES (?, ?)')->execute(['prototype@example.test', password_hash('SyntheticPassword123!', PASSWORD_BCRYPT)]);
        $source->prepare('INSERT INTO staff_members VALUES (?, ?, ?, ?, ?)')->execute([
            'Example', 'Coach', $invalidStaff ? 'invalid' : 'staff@example.test', 'Sport', 'Coach',
        ]);
        $this->app->instance('prototype.source', $source);
        $this->sourceConfig = tempnam(sys_get_temp_dir(), 'sports-prototype-');
        file_put_contents($this->sourceConfig, '<?php $connection = app("prototype.source");');

        return $this->sourceConfig;
    }

    protected function tearDown(): void
    {
        if ($this->sourceConfig !== null && is_file($this->sourceConfig)) {
            unlink($this->sourceConfig);
        }
        parent::tearDown();
    }

    public function test_import_preserves_hashes_and_repeated_imports_keep_existing_changes(): void
    {
        $file = $this->source();
        $this->artisan('prototype:import', ['--config' => $file, '--school' => 'Example School'])->assertSuccessful();
        $user = User::where('email', 'prototype@example.test')->sole();
        $this->assertTrue(password_verify('SyntheticPassword123!', $user->password));
        $this->assertSame('principal', $user->schools()->sole()->pivot->role);
        $staff = StaffMember::sole();
        $staff->update(['position' => 'Updated position']);
        $this->artisan('prototype:import', ['--config' => $file, '--school' => 'Example School'])->assertSuccessful();
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('schools', 1);
        $this->assertDatabaseCount('staff_members', 1);
        $this->assertSame('Updated position', $staff->fresh()->position);
    }

    public function test_invalid_source_rolls_back_all_destination_changes(): void
    {
        $this->artisan('prototype:import', ['--config' => $this->source(true), '--school' => 'Example School'])->assertFailed();
        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('schools', 0);
        $this->assertDatabaseCount('staff_members', 0);
    }

    public function test_import_refuses_ambiguous_school_names(): void
    {
        School::factory()->count(2)->create(['name' => 'Same school name']);
        $this->artisan('prototype:import', ['--config' => $this->source(), '--school' => 'Same school name'])->assertFailed();
        $this->assertDatabaseCount('users', 0);
    }
}
