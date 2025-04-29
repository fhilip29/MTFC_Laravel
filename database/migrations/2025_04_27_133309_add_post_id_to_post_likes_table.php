<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('post_likes', function (Blueprint $table) {
            if (!Schema::hasColumn('post_likes', 'post_id')) {
                $table->unsignedBigInteger('post_id');
                $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
            }
        });
    }

    public function down(): void
    {
        Schema::table('post_likes', function (Blueprint $table) {
            if (Schema::hasColumn('post_likes', 'post_id')) {
                $table->dropForeign(['post_id']);
                $table->dropColumn('post_id');
            }
        });
    }
};
