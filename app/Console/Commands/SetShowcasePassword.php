<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Support\Facades\DB;

class SetShowcasePassword extends Command
{
    use ConfirmableTrait;

    protected $signature = 'showcase:password {--force : Allow password changes on the explicitly enabled hosted demo}';

    protected $description = 'Privately set a password for existing, unchanged showcase login accounts';

    public function handle(): int
    {
        if (! app()->environment(['local', 'testing']) && config('app.showcase_enabled') !== true) {
            $this->error('Enable SHOWCASE_ENABLED=true only on your dedicated demo before changing showcase passwords.');

            return self::FAILURE;
        }
        if (! $this->confirmToProceed()) {
            return self::FAILURE;
        }
        $password = $this->secret('New showcase password');
        if (! is_string($password) || strlen($password) < 8 || strlen($password) > 72) {
            $this->error('Use a password between 8 and 72 bytes. No accounts were changed.');

            return self::FAILURE;
        }
        $updated = DB::transaction(function () use ($password): int {
            $updated = 0;
            foreach (['principal', 'deputy', 'hr', 'coach', 'student', 'guardian'] as $role) {
                // Matching emails alone must not reset accounts preserved by the showcase seeder.
                $user = User::where('email', $role.'@showcase.example.invalid')
                    ->where('name', 'Showcase '.ucfirst($role))->lockForUpdate()->first();
                if ($user) {
                    $user->forceFill(['password' => $password, 'remember_token' => null])->save();
                    $updated++;
                }
            }

            return $updated;
        });
        if ($updated === 0) {
            $this->error('No unchanged showcase login accounts found. Seed the showcase first.');

            return self::FAILURE;
        }
        $this->info('Updated '.$updated.' showcase login password(s). Other accounts and memberships were preserved.');

        return self::SUCCESS;
    }
}
