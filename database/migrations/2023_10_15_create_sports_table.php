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
        Schema::create('sports', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique(); // Used in routes: gym, boxing, muay, jiu-jitsu
            $table->text('description')->nullable();
            $table->string('background_image')->nullable();
            $table->text('short_description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('display_order')->default(0);
            $table->timestamps();
        });
        
        // Insert default sports
        DB::table('sports')->insert([
            [
                'name' => 'Gym',
                'slug' => 'gym',
                'description' => 'Access state-of-the-art gym facilities and equipment',
                'background_image' => '/assets/gym-bg.jpg',
                'short_description' => 'Access state-of-the-art gym facilities and equipment',
                'is_active' => true,
                'display_order' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Boxing',
                'slug' => 'boxing',
                'description' => 'Train with professional boxing coaches in our dedicated boxing facilities',
                'background_image' => '/assets/gym-bg.jpg',
                'short_description' => 'Train with professional boxing coaches in our dedicated boxing facilities',
                'is_active' => true,
                'display_order' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Muay Thai',
                'slug' => 'muay',
                'description' => 'Train in the art of eight limbs with experienced Muay Thai instructors',
                'background_image' => '/assets/gym-bg.jpg',
                'short_description' => 'Train in the art of eight limbs with experienced Muay Thai instructors',
                'is_active' => true,
                'display_order' => 3,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Jiu Jitsu',
                'slug' => 'jiu',
                'description' => 'Learn Brazilian Jiu Jitsu from certified black belt instructors',
                'background_image' => '/assets/gym-bg.jpg',
                'short_description' => 'Learn Brazilian Jiu Jitsu from certified black belt instructors',
                'is_active' => true,
                'display_order' => 4,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sports');
    }
}; 