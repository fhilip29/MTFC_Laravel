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
        // Create post_tags table if it doesn't exist
        if (!Schema::hasTable('post_tags')) {
            Schema::create('post_tags', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->timestamps();
            });
            
            // Seed initial tags
            $tags = [
                ['name' => 'Workouts', 'slug' => 'workouts', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Muay Thai', 'slug' => 'muay-thai', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Boxing', 'slug' => 'boxing', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Jiu-Jitsu', 'slug' => 'jiu-jitsu', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Nutrition', 'slug' => 'nutrition', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Cardio', 'slug' => 'cardio', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Strength Training', 'slug' => 'strength-training', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Weight Loss', 'slug' => 'weight-loss', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Community Events', 'slug' => 'community-events', 'created_at' => now(), 'updated_at' => now()]
            ];
            
            DB::table('post_tags')->insert($tags);
        }
        
        // Create post_tag pivot table if it doesn't exist
        if (!Schema::hasTable('post_tag')) {
            Schema::create('post_tag', function (Blueprint $table) {
                $table->foreignId('post_id')->constrained()->onDelete('cascade');
                $table->foreignId('post_tag_id')->constrained('post_tags')->onDelete('cascade');
                $table->primary(['post_id', 'post_tag_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_tag');
        Schema::dropIfExists('post_tags');
    }
};
