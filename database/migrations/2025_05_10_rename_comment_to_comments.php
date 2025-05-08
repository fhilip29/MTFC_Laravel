<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class RenameCommentToComments extends Migration
{
    public function up()
    {
        // Check if 'comment' table exists and 'comments' table doesn't exist
        if (Schema::hasTable('comment') && !Schema::hasTable('comments')) {
            // Rename the table from comment to comments
            Schema::rename('comment', 'comments');
        }
    }

    public function down()
    {
        // If you need to rollback, rename it back to comment
        if (Schema::hasTable('comments') && !Schema::hasTable('comment')) {
            Schema::rename('comments', 'comment');
        }
    }
} 