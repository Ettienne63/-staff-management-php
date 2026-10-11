<?php

namespace App\Console\Commands;

use App\Models\School;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PDO;
use RuntimeException;
use Throwable;

class ImportPrototype extends Command
{
    protected $signature = 'prototype:import {--config= : Archived PHP database configuration} {--school= : Explicit destination school name}';

    protected $description = 'Import local prototype administrators and staff without modifying the source database';

    public function handle(): int
    {
        if (! app()->environment(['local', 'testing'])) {
            $this->error('Prototype import is restricted to local and testing environments.');

            return self::FAILURE;
        }
        $file = $this->option('config');
        $name = trim((string) $this->option('school'));
        if (! is_string($file) || ! is_file($file) || $name === '' || mb_strlen($name) > 255) {
            $this->error('Provide a local --config file and a destination --school name.');

            return self::FAILURE;
        }
        try {
            $source = (static function (string $path): PDO {
                require $path;
                if (! isset($connection) || ! $connection instanceof PDO) {
                    throw new RuntimeException('Source connection unavailable.');
                }

                return $connection;
            })($file);
            $source->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $users = $source->query('SELECT email, password FROM users')->fetchAll(PDO::FETCH_ASSOC);
            $staff = $source->query('SELECT first_name, last_name, email, department, position FROM staff_members')->fetchAll(PDO::FETCH_ASSOC);
            DB::transaction(function () use ($name, $users, $staff): void {
                $matches = School::where('name', $name)->get();
                if ($matches->count() > 1) {
                    throw new RuntimeException('Ambiguous school name.');
                }
                $school = $matches->first() ?? School::create(['name' => $name]);
                foreach ($users as $record) {
                    Validator::make($record, ['email' => 'required|email|max:255', 'password' => 'required|string'])->validate();
                    if (password_get_info($record['password'])['algo'] === null) {
                        throw new RuntimeException('Source password must be hashed.');
                    }
                    $user = User::firstOrNew(['email' => $record['email']]);
                    if (! $user->exists) {
                        $user->setRawAttributes([
                            'name' => $record['email'],
                            'email' => $record['email'],
                            'password' => $record['password'],
                        ]);
                        $user->save();
                    }
                    if (! $user->schools()->whereKey($school->id)->exists()) {
                        if ($user->schools()->wherePivotIn('role', User::LeadershipRoles)->exists()) {
                            throw new RuntimeException('An imported account already has a leadership role at another school.');
                        }
                        $user->schools()->attach($school, ['role' => 'principal']);
                    }
                }
                foreach ($staff as $record) {
                    Validator::make($record, [
                        'first_name' => 'required|string|max:100',
                        'last_name' => 'required|string|max:100',
                        'email' => 'required|email|max:255',
                        'department' => 'required|string|max:150',
                        'position' => 'required|string|max:150',
                    ])->validate();
                    $school->staffMembers()->firstOrCreate(['email' => $record['email']], $record);
                }
            });
        } catch (Throwable) {
            $this->error('Import failed. No destination changes were saved; check the source and school name.');

            return self::FAILURE;
        }
        $this->info('Imported '.count($users).' prototype account(s) and '.count($staff).' staff record(s). Existing destination records were preserved.');

        return self::SUCCESS;
    }
}
