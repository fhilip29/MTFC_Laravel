<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class RenamePostCommentsToComments extends Migration
{
    public function up()
    {
        // Rename the table from post_comments to comments
        Schema::rename('post_comments', 'comments');
    }

    public function down()
    {
        // If you need to rollback, rename it back to post_comments
        Schema::rename('comments', 'post_comments');
    }
}
