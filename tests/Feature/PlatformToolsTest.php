<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\StaffMember;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Tests\TestCase;

class PlatformToolsTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $admin = User::factory()->create();
        config(['app.platform_admin_email' => $admin->email]);
        $this->actingAs($admin);

        return $admin;
    }

    public function test_school_registration_and_public_corrections_are_audited_without_principal_identity(): void
    {
        $admin = $this->admin();
        $principal = User::factory()->create();
        $this->post(route('internal.schools.store'), [
            'name' => 'Registration school', 'principal_email' => $principal->email,
            'public_email' => 'office@example.test', 'public_phone' => '0123456789', 'website' => 'https://example.test',
        ])->assertRedirect(route('internal.schools.index'));
        $school = School::sole();
        $this->assertDatabaseHas('platform_activities', ['actor_id' => $admin->id, 'school_id' => $school->id, 'action' => 'school.created']);
        $this->get(route('internal.schools.index'))->assertOk()->assertSee('Leadership assigned')->assertSee('Public contact provided')
            ->assertSee('office@example.test')->assertDontSee($principal->email)->assertDontSee($principal->name);
        $this->get(route('internal.schools.edit', $school))->assertOk()->assertDontSee($principal->email);
        $this->put(route('internal.schools.update', $school), [
            'name' => 'Corrected school', 'public_email' => 'new-office@example.test',
            'principal_email' => $admin->email, 'user_id' => $admin->id,
        ])->assertRedirect(route('internal.schools.index'));
        $this->assertDatabaseHas('schools', ['id' => $school->id, 'name' => 'Corrected school', 'public_email' => 'new-office@example.test']);
        $this->assertDatabaseHas('school_user', ['school_id' => $school->id, 'user_id' => $principal->id, 'role' => 'principal']);
        $this->assertDatabaseMissing('school_user', ['user_id' => $admin->id]);
        $this->assertDatabaseHas('platform_activities', ['action' => 'school.updated']);
        $this->get(route('internal.activity'))->assertOk()->assertSee('school.created')->assertSee('Corrected school')
            ->assertDontSee($principal->email)->assertDontSee($principal->name);
        $principal->schools()->detach($school);
        $this->get(route('internal.schools.index'))->assertSee('Needs leadership assignment');
    }

    public function test_public_details_validation_preserves_school_and_does_not_record_failed_changes(): void
    {
        $this->admin();
        $first = School::factory()->create();
        $other = School::factory()->create();
        $this->put(route('internal.schools.update', $first), ['name' => $other->name, 'website' => 'javascript:alert(1)', 'public_email' => 'bad'])
            ->assertSessionHasErrors(['name', 'website', 'public_email']);
        $this->assertDatabaseHas('schools', ['id' => $first->id, 'name' => $first->name]);
        $this->assertDatabaseCount('platform_activities', 0);
        $this->put(route('internal.schools.update', $first), ['name' => $first->name])->assertRedirect();
        $this->assertDatabaseCount('platform_activities', 0);
    }

    public function test_notices_are_only_visible_to_leadership_and_disappear_on_withdrawal_or_expiry(): void
    {
        $admin = $this->admin();
        $schools = School::factory()->count(2)->create();
        $this->post(route('internal.announcements.store'), [
            'title' => 'Maintenance notice', 'body' => '<script>alert(1)</script>', 'expires_at' => now()->addDays(2)->format('Y-m-d'),
        ])->assertRedirect(route('internal.announcements.index'));
        $id = DB::table('platform_announcements')->value('id');
        $this->get(route('internal.announcements.index'))->assertOk()->assertSee('Maintenance notice')
            ->assertSee('&lt;script&gt;', false)->assertDontSee('<script>alert(1)</script>', false);
        foreach ($schools as $school) {
            foreach (['principal', 'deputy', 'hr', 'coach', 'student'] as $role) {
                $user = User::factory()->create();
                $user->schools()->attach($school, ['role' => $role]);
                $response = $this->actingAs($user)->get(route('schools.workspace', $school))->assertOk();
                if (in_array($role, User::LeadershipRoles, true)) {
                    $response->assertSee('Maintenance notice')->assertSee('&lt;script&gt;', false)->assertDontSee('<script>alert(1)</script>', false);
                } else {
                    $response->assertDontSee('Maintenance notice');
                }
            }
        }
        $guardian = User::factory()->create();
        $this->actingAs($guardian)->get(route('family.dashboard'))->assertDontSee('Maintenance notice');
        $this->actingAs($admin)->delete(route('internal.announcements.destroy', $id))->assertRedirect();
        $this->delete(route('internal.announcements.destroy', $id))->assertRedirect();
        $this->get(route('internal.announcements.index'))->assertOk()->assertSee('Withdrawn');
        $this->assertDatabaseCount('platform_activities', 2);
        $leader = $schools->first()->users()->wherePivot('role', 'principal')->first();
        $this->actingAs($leader)->get(route('schools.workspace', $schools->first()))->assertDontSee('Maintenance notice');
        $this->actingAs($admin)->post(route('internal.announcements.store'), [
            'title' => 'Expiring notice', 'body' => 'Test message', 'expires_at' => now()->addDays(2)->format('Y-m-d'),
        ])->assertRedirect();
        $this->travel(3)->days();
        $this->actingAs($leader)->get(route('schools.workspace', $schools->first()))->assertDontSee('Expiring notice');
    }

    public function test_health_has_honest_backup_reports_and_no_private_records_or_configuration(): void
    {
        $this->admin();
        $school = School::factory()->create();
        $staff = StaffMember::factory()->for($school)->create();
        $student = Student::factory()->for($school)->create();
        $this->get(route('internal.health'))->assertOk()->assertSee('Not reported')->assertSee('Available')
            ->assertDontSee($staff->email)->assertDontSee($student->first_name)->assertDontSee(base_path());
        $this->artisan('platform:record-backup', ['result' => 'success'])->assertSuccessful();
        $this->get(route('internal.health'))->assertSee('Success reported');
        $this->artisan('platform:record-backup', ['result' => 'invalid'])->assertFailed();
        $this->assertDatabaseCount('platform_backup_reports', 1);
        $this->artisan('platform:record-backup', ['result' => 'failure'])->assertSuccessful();
        $this->get(route('internal.health'))->assertSee('Failure reported');
    }

    public function test_every_platform_tool_requires_the_current_designated_admin(): void
    {
        $school = School::factory()->create();
        $user = User::factory()->create();
        $user->schools()->attach($school, ['role' => 'principal']);
        $this->get(route('internal.health'))->assertRedirect(route('login'));
        $this->actingAs($user);
        foreach (['internal.health', 'internal.activity', 'internal.announcements.index'] as $route) {
            $this->get(route($route))->assertForbidden();
        }
        $this->get(route('internal.schools.edit', $school))->assertForbidden();
        $this->put(route('internal.schools.update', $school), ['name' => 'Denied'])->assertForbidden();
        $this->post(route('internal.announcements.store'), [])->assertForbidden();
        $this->delete(route('internal.announcements.destroy', 1))->assertForbidden();
        config(['app.platform_admin_email' => $user->email]);
        $this->get(route('internal.health'))->assertOk();
        config(['app.platform_admin_email' => null]);
        $this->get(route('internal.health'))->assertForbidden();
        $this->assertDatabaseCount('platform_activities', 0);
        $this->assertDatabaseCount('platform_announcements', 0);
    }

    public function test_invalid_notices_and_missing_withdrawal_do_not_write_audit_or_notice(): void
    {
        $this->admin();
        $this->post(route('internal.announcements.store'), ['title' => '', 'body' => str_repeat('x', 2001), 'expires_at' => now()->format('Y-m-d')])
            ->assertSessionHasErrors(['title', 'body', 'expires_at']);
        $this->delete(route('internal.announcements.destroy', 999))->assertNotFound();
        $this->assertDatabaseCount('platform_activities', 0);
        $this->assertDatabaseCount('platform_announcements', 0);
    }

    public function test_public_correction_rolls_back_if_its_audit_cannot_be_saved(): void
    {
        $this->admin();
        $school = School::factory()->create();
        $failAudit = true;
        DB::listen(function (QueryExecuted $query) use (&$failAudit): void {
            if ($failAudit && str_contains($query->sql, 'insert into') && str_contains($query->sql, 'platform_activities')) {
                $failAudit = false;
                throw new RuntimeException('Audit storage failed');
            }
        });
        $this->withoutExceptionHandling();
        try {
            $this->put(route('internal.schools.update', $school), ['name' => 'Must roll back']);
            $this->fail('An audit failure must abort the correction.');
        } catch (RuntimeException $exception) {
            $this->assertSame('Audit storage failed', $exception->getMessage());
        }
        $this->assertDatabaseHas('schools', ['id' => $school->id, 'name' => $school->name]);
        $this->assertDatabaseCount('platform_activities', 0);
    }
}
