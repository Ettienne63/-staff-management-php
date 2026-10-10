<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schools', function (Blueprint $table): void {
            $table->string('public_email')->nullable();
            $table->string('public_phone', 40)->nullable();
            $table->string('website')->nullable();
        });
        Schema::create('platform_activities', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('school_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 60);
            $table->json('details');
            $table->timestamp('created_at');
        });
        Schema::create('platform_announcements', function (Blueprint $table): void {
            $table->id();
            $table->string('title', 150);
            $table->text('body');
            $table->timestamp('published_at');
            $table->timestamp('expires_at');
            $table->timestamp('withdrawn_at')->nullable();
        });
        Schema::create('platform_backup_reports', function (Blueprint $table): void {
            $table->id();
            $table->string('result', 10);
            $table->timestamp('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('platform_backup_reports');
        Schema::dropIfExists('platform_announcements');
        Schema::dropIfExists('platform_activities');
        Schema::table('schools', function (Blueprint $table): void {
            $table->dropColumn(['public_email', 'public_phone', 'website']);
        });
    }
};
