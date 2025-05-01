<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sessions extends Model
{
    protected $fillable = ['user_id', 'guest_name', 'mobile_number', 'time', 'status'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
