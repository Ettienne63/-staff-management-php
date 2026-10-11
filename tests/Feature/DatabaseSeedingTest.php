<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeedingTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_seeding_does_not_create_accounts_or_school_records(): void
    {
        $this->seed();

        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('schools', 0);
        $this->assertDatabaseCount('school_user', 0);
        $this->assertDatabaseCount('staff_members', 0);
    }
}
