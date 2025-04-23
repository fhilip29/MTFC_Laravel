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
        Schema::create('trainers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Linked to users table

            $table->string('profile_url')->nullable(); 
            $table->text('short_intro')->nullable(); 
            $table->string('instructor_schedule')->nullable(); 
            $table->decimal('hourly_rate', 10, 2)->nullable(); 
            $table->string('specialization')->nullable(); 
            $table->string('instructor_for')->nullable(); 

            $table->timestamps(); 
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trainers');
    }
};
