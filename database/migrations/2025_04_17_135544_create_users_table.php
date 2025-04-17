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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
    
            // Core Info
            $table->string('full_name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('mobile_number')->nullable();
    
            // Profile
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->enum('fitness_goal', ['lose-weight', 'build-muscle', 'maintain'])->nullable();
            $table->string('profile_image')->nullable();
            $table->string('qr_code')->nullable();
    
            // Roles & Terms
            $table->enum('role', ['member', 'trainer', 'admin'])->default('member');
            $table->boolean('is_agreed_to_terms')->default(false);
    
            // Laravel Fields
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
