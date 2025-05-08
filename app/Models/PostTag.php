<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostTag extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug'];

    /**
     * The posts that belong to the tag
     */
    public function posts()
    {
        return $this->belongsToMany(Post::class, 'post_tag', 'post_tag_id', 'post_id');
    }
} 