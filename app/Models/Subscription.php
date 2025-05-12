<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Subscription extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'type',
        'plan',
        'price',
        'amount',
        'start_date',
        'end_date',
        'is_active',
        'payment_method',
        'payment_status',
        'payment_reference',
        'waiver_accepted',
        'cancelled_at',
        'sessions_remaining',
        'sessions_used'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user that owns the subscription.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the subscription is currently active.
     */
    public function isActive()
    {
        if (!$this->is_active) {
            return false;
        }
        
        // For per-session, check sessions_remaining
        if ($this->plan === 'per-session') {
            // If sessions_remaining is null, it means unlimited (legacy data)
            if ($this->sessions_remaining === null) {
                return true;
            }
            // Otherwise, check if there are sessions remaining
            return $this->sessions_remaining > 0;
        }
        
        // For time-based plans, check if end_date is in the future
        return $this->end_date && $this->end_date->gt(Carbon::now());
    }

    /**
     * Get the invoices for the subscription.
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Use a session from the subscription.
     * 
     * @return bool Whether the session was successfully used
     */
    public function useSession()
    {
        // Only applicable for per-session plans
        if ($this->plan !== 'per-session') {
            return false;
        }
        
        // Check if active
        if (!$this->is_active) {
            return false;
        }
        
        // If sessions_remaining is null (unlimited or legacy data)
        if ($this->sessions_remaining === null) {
            $this->sessions_used += 1;
            $this->save();
            return true;
        }
        
        // Check if there are sessions remaining
        if ($this->sessions_remaining <= 0) {
            return false;
        }
        
        // Use a session
        $this->sessions_remaining -= 1;
        $this->sessions_used += 1;
        
        // If no sessions left, mark as inactive
        if ($this->sessions_remaining <= 0) {
            $this->is_active = false;
        }
        
        $this->save();
        return true;
    }
}
