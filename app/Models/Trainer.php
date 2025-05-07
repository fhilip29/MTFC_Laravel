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

    protected $appends = ['profile_image_url'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

     public function schedules()
    {
     return $this->hasMany(TrainerSchedule::class, 'trainer_id');
    }

    public function getProfileImageUrlAttribute()
    {
        if (empty($this->profile_url)) {
            return asset('assets/default-profile.jpg');
        }
        
        // Check if profile_url is already a full URL or base64 image
        if (filter_var($this->profile_url, FILTER_VALIDATE_URL) || 
            strpos($this->profile_url, 'data:image') === 0) {
            return $this->profile_url;
        }
        
        // Check if path starts with storage/ or public/
        if (strpos($this->profile_url, 'storage/') === 0 || 
            strpos($this->profile_url, 'public/') === 0) {
            return asset($this->profile_url);
        }
        
        // Default case - prepend storage path
        return asset($this->profile_url);
    }
}