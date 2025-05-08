<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreatePostTagsTable extends Migration
{
    public function up()
    {
        Schema::create('post_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });
        
        // Create pivot table for post-tag relationship
        Schema::create('post_tag', function (Blueprint $table) {
            $table->foreignId('post_id')->constrained()->onDelete('cascade');
            $table->foreignId('post_tag_id')->constrained()->onDelete('cascade');
            $table->primary(['post_id', 'post_tag_id']);
        });
        
        // Seed initial tags
        $tags = [
            ['name' => 'Workouts', 'slug' => 'workouts'],
            ['name' => 'Muay Thai', 'slug' => 'muay-thai'],
            ['name' => 'Boxing', 'slug' => 'boxing'],
            ['name' => 'Jiu-Jitsu', 'slug' => 'jiu-jitsu'],
            ['name' => 'Nutrition', 'slug' => 'nutrition'],
            ['name' => 'Cardio', 'slug' => 'cardio'],
            ['name' => 'Strength Training', 'slug' => 'strength-training'],
            ['name' => 'Weight Loss', 'slug' => 'weight-loss'],
            ['name' => 'Community Events', 'slug' => 'community-events']
        ];
        
        $postsTable = DB::table('post_tags');
        foreach ($tags as $tag) {
            $postsTable->insert($tag);
        }
    }

    public function down()
    {
        Schema::dropIfExists('post_tag');
        Schema::dropIfExists('post_tags');
    }
} 