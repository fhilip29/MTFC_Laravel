<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PostImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'path',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
