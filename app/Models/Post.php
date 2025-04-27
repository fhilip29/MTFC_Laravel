<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = ['content', 'user_id'];

    // Define the user who created the post
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Define the images associated with a post
    public function images()
    {
        return $this->hasMany(PostImage::class);
    }

    // Define the many-to-many relationship for likes (relationship name: likes)
    public function likes()
    {
        return $this->belongsToMany(User::class, 'post_likes');
    }

    // Define the comments associated with the post
    public function comments()
    {
        return $this->hasMany(PostComment::class);
    }
}