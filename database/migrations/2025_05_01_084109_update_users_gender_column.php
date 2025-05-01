<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, convert enum to varchar to store custom gender values
        DB::statement("ALTER TABLE users MODIFY COLUMN gender VARCHAR(255)");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to the original enum with only male/female options
        // Note: This will work only if all values in the column are either male, female, or null
        DB::statement("ALTER TABLE users MODIFY COLUMN gender ENUM('male', 'female', 'other') NULL");
    }
};
