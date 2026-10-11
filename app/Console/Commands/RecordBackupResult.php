<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RecordBackupResult extends Command
{
    protected $signature = 'platform:record-backup {result : success or failure}';

    protected $description = 'Record the result of an external backup process without storing backup contents';

    public function handle(): int
    {
        $result = $this->argument('result');
        if (! in_array($result, ['success', 'failure'], true)) {
            $this->error('Use success or failure. This command reports a result; it does not create a backup.');

            return self::FAILURE;
        }
        DB::table('platform_backup_reports')->insert(['result' => $result, 'created_at' => now()]);
        $this->info('Backup result recorded.');

        return self::SUCCESS;
    }
}
