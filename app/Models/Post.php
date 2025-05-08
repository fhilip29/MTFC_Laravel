<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = ['content', 'user_id', 'tag_ids'];

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

    // Define the likes relationship
    public function likes()
    {
        return $this->hasMany(PostLike::class);
    }

    // Define the comments associated with the post
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likedByUsers()
    {
        return $this->belongsToMany(User::class, 'post_likes')->withTimestamps();
    }
    
    /**
     * The tags that belong to the post
     */
    public function tags()
    {
        return $this->belongsToMany(PostTag::class, 'post_tag', 'post_id', 'post_tag_id');
    }
}