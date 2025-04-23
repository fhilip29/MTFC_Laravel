<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Trainer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'profile_url',
        'short_intro',
        'instructor_schedule',
        'hourly_rate',
        'specialization',
        'instructor_for',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

     public function schedules()
    {
     return $this->hasMany(TrainerSchedule::class, 'trainer_id');
    }
}