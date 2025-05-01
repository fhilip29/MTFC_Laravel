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
        Schema::table('sessions', function (Blueprint $table) {
            // Add guest_name column
            $table->string('guest_name')->nullable()->after('user_id');
            
            // Make user_id nullable for guest entries
            $table->foreignId('user_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sessions', function (Blueprint $table) {
            // Remove the guest_name column
            $table->dropColumn('guest_name');
            
            // Revert user_id to not nullable
            $table->foreignId('user_id')->nullable(false)->change();
        });
    }
}; 