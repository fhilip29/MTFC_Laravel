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
        Schema::table('trainers', function (Blueprint $table) {
            // Check if profile_url column exists and is of the right type
            if (Schema::hasColumn('trainers', 'profile_url')) {
                // Modify to be a string of 255 characters
                $table->string('profile_url', 255)->nullable()->change();
            } else {
                // Add column if it doesn't exist
                $table->string('profile_url', 255)->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No action needed for rollback, as we're just ensuring the column exists
    }
};
