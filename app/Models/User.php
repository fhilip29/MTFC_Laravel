<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'full_name',
        'email',
        'password',
        'mobile_number',
        'gender',
        'fitness_goal',
        'profile_image',
        'qr_code',
        'role',
        'is_agreed_to_terms',
        'is_archived',
    ];
    

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_agreed_to_terms' => 'boolean',
        'is_archived' => 'boolean',
    ];
    
    /**
     * Check if user is an admin
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
    
    /**
     * Check if user is a trainer
     *
     * @return bool
     */
    public function isTrainer(): bool
    {
        return $this->role === 'trainer';
    }
    
    /**
     * Check if user is a regular member
     *
     * @return bool
     */
    public function isMember(): bool
    {
        return $this->role === 'member';
    }

     //subscriptions
     public function subscriptions()
     {
         return $this->hasMany(Subscription::class);
     }

     public function activeSubscriptions()
     {
         return $this->subscriptions()->where('is_active', true)
             ->where(function($query) {
                 // Either end_date is null (per-session) or end_date is in the future
                 $query->whereNull('end_date')
                       ->orWhere('end_date', '>', now());
             });
     }

     /**
      * Check if user has active subscription for a specific type
      */
     public function hasActiveSubscription($type)
     {
         return $this->activeSubscriptions()
             ->where('type', $type)
             ->exists();
     }

/**
 * Scope a query to only include non-archived users.
 *
 * @param  \Illuminate\Database\Eloquent\Builder  $query
 * @return \Illuminate\Database\Eloquent\Builder
 */
public function scopeNotArchived($query)
{
    return $query->where('is_archived', false);
}

/**
 * Scope a query to only include archived users.
 *
 * @param  \Illuminate\Database\Eloquent\Builder  $query
 * @return \Illuminate\Database\Eloquent\Builder
 */
public function scopeArchived($query)
{
    return $query->where('is_archived', true);
}

//Sessions
public function sessions()
{
    return $this->hasMany(Sessions::class);
}

//Cart
public function cart()
{
    return $this->hasOne(Cart::class);
}

public function trainer()
{
    return $this->hasOne(Trainer::class);
}

public function invoices()
{
    return $this->hasMany(Invoice::class);
}

public function likedPosts()
{
    return $this->belongsToMany(Post::class, 'post_likes')->withTimestamps();
}

public function likedPostsCount()
{
    return $this->likedPosts()->count();
}

/**
 * Get the messages sent by the user.
 */
public function sentMessages()
{
    return $this->hasMany(Message::class, 'sender_id');
}

/**
 * Get the messages received by the user.
 */
public function receivedMessages()
{
    return $this->hasMany(Message::class, 'recipient_id');
}

/**
 * Get the sports this user (trainer) specializes in
 */
public function specialtySports()
{
    return $this->belongsToMany(Sport::class, 'trainer_specialties');
}

}
