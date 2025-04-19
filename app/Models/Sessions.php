<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sessions extends Model
{
    protected $fillable = ['user_id', 'time', 'status'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
