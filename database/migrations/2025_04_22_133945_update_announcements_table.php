<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            if (!Schema::hasColumn('announcements', 'title')) {
                $table->string('title');
            }
            if (!Schema::hasColumn('announcements', 'message')) {
                $table->text('message');
            }
            if (!Schema::hasColumn('announcements', 'target_audience')) {
                $table->enum('target_audience', ['all', 'active', 'trainers', 'staff'])->default('all');
            }
            if (!Schema::hasColumn('announcements', 'send_email')) {
                $table->boolean('send_email')->default(false);
            }
            if (!Schema::hasColumn('announcements', 'send_in_app')) {
                $table->boolean('send_in_app')->default(true);
            }
            if (!Schema::hasColumn('announcements', 'scheduled_at')) {
                $table->timestamp('scheduled_at')->nullable();
            }
            if (!Schema::hasColumn('announcements', 'sent_at')) {
                $table->timestamp('sent_at')->nullable();
            }
            if (!Schema::hasColumn('announcements', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }
            if (!Schema::hasColumn('announcements', 'created_by')) {
                $table->foreignId('created_by')->constrained('users');
            }
            if (!Schema::hasColumn('announcements', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropIfExists(['title', 'message', 'target_audience', 'send_email', 'send_in_app', 
                'scheduled_at', 'sent_at', 'is_active', 'created_by', 'deleted_at']);
        });
    }
};
