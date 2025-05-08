<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostLike extends Model
{
    protected $fillable = ['post_id', 'user_id'];

    // Define relationship with User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Define relationship with Post model
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}