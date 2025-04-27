<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostLikesTable extends Migration
{
    public function up()
{
    Schema::create('post_likes', function (Blueprint $table) {
        $table->id();
        $table->foreignId('post_id')->constrained('posts')->onDelete('cascade'); // Ensures the post_id is properly set
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Associate the like with a user
        $table->timestamps();
    });
}

    public function down()
    {
        Schema::dropIfExists('post_likes');
    }
}
