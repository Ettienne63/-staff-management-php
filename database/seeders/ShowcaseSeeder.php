<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class ShowcaseSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new RuntimeException('Showcase data is available only in local and testing environments.');
        }

        $password = Str::password(24);
        $createdEmails = [];

        DB::transaction(function () use ($password, &$createdEmails): void {
            $schools = collect(['Showcase Oakfield School', 'Showcase Riverbend School'])
                ->map(fn (string $name): School => School::firstOrCreate(['name' => $name]));

            foreach (['principal', 'deputy', 'hr', 'coach', 'student', 'guardian'] as $role) {
                $email = $role.'@showcase.example.invalid';
                $user = User::firstOrCreate(['email' => $email], [
                    'name' => 'Showcase '.ucfirst($role),
                    'password' => $password,
                ]);

                if ($user->wasRecentlyCreated) {
                    $createdEmails[] = $email;

                    if ($role !== 'guardian') {
                        foreach ($role === 'coach' ? $schools : $schools->take(1) as $school) {
                            $user->schools()->attach($school, ['role' => $role]);
                        }
                    }
                }
            }

            foreach ($schools as $school) {
                foreach ([
                    ['Avery', 'Demo', 'Athletics', 'Coach'],
                    ['Jordan', 'Sample', 'Swimming', 'Coach'],
                    ['Morgan', 'Example', 'Football', 'Coach'],
                    ['Taylor', 'Preview', 'School leadership', 'Deputy principal'],
                    ['Casey', 'Fixture', 'Administration', 'HR coordinator'],
                    ['Riley', 'Showcase', 'Netball', 'Coach'],
                ] as $index => [$firstName, $lastName, $department, $position]) {
                    $school->staffMembers()->firstOrCreate([
                        'email' => 'staff'.($index + 1).'@showcase.example.invalid',
                    ], [
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'department' => $department,
                        'position' => $position,
                    ]);
                }
            }
        });

        if ($createdEmails !== []) {
            $this->command?->info('New showcase accounts: '.implode(', ', $createdEmails));
            $this->command?->info('Password for these new accounts: '.$password);
        } else {
            $this->command?->info('Showcase accounts already exist; passwords and memberships were preserved.');
        }

        $this->command?->info('Sports and guardian-linking pages remain empty until their workflows are implemented.');
    }
}
