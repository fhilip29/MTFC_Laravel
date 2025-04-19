<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = [
        'user_id', 'items'
    ];

    // Store items as JSON in the database
    protected $casts = [
        'items' => 'array',
    ];

    // Relationship with user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 