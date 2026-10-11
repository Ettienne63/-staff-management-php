<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Throwable;

class PlatformController extends Controller
{
    public function activity(): View
    {
        return view('internal.activity', ['activities' => DB::table('platform_activities')->orderByDesc('id')->paginate(30)]);
    }

    public function health(): View
    {
        $databaseAvailable = false;
        $backup = null;
        try {
            DB::select('SELECT 1');
            $databaseAvailable = true;
            $backup = DB::table('platform_backup_reports')->orderByDesc('id')->first();
        } catch (Throwable) {
            $databaseAvailable = false;
        }
        $freeBytes = @disk_free_space(storage_path());

        return view('internal.health', [
            'databaseAvailable' => $databaseAvailable,
            'storageWritable' => is_writable(storage_path('framework')) && is_writable(storage_path('logs')),
            'freeGigabytes' => $freeBytes === false ? null : round($freeBytes / 1073741824, 1),
            'backup' => $backup,
        ]);
    }

    public function announcements(): View
    {
        return view('internal.announcements', ['announcements' => DB::table('platform_announcements')->orderByDesc('id')->paginate(20)]);
    }

    public function publish(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'body' => ['required', 'string', 'max:2000'],
            'expires_at' => ['required', 'date_format:Y-m-d', 'after:today'],
        ]);
        DB::transaction(function () use ($request, $data): void {
            $id = DB::table('platform_announcements')->insertGetId($data + ['published_at' => now()]);
            DB::table('platform_activities')->insert([
                'actor_id' => $request->user()->id, 'action' => 'announcement.published',
                'details' => json_encode(['announcement_id' => $id, 'title' => $data['title']], JSON_THROW_ON_ERROR), 'created_at' => now(),
            ]);
        });

        return redirect()->route('internal.announcements.index')->with('status', 'Announcement published for school leadership.');
    }

    public function withdraw(Request $request, int $announcement): RedirectResponse
    {
        DB::transaction(function () use ($request, $announcement): void {
            $notice = DB::table('platform_announcements')->where('id', $announcement)->lockForUpdate()->first();
            abort_unless($notice, 404);
            if ($notice->withdrawn_at !== null) {
                return;
            }
            DB::table('platform_announcements')->where('id', $announcement)->update(['withdrawn_at' => now()]);
            DB::table('platform_activities')->insert([
                'actor_id' => $request->user()->id, 'action' => 'announcement.withdrawn',
                'details' => json_encode(['announcement_id' => $announcement, 'title' => $notice->title], JSON_THROW_ON_ERROR), 'created_at' => now(),
            ]);
        });

        return redirect()->route('internal.announcements.index')->with('status', 'Announcement withdrawn.');
    }
}
