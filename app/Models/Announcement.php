<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Announcement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'message',
        'scheduled_at',
        'sent_at',
        'is_active',
        'created_by'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    protected $dates = [
        'scheduled_at',
        'sent_at',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeScheduled($query)
    {
        return $query->whereNotNull('scheduled_at')->where('scheduled_at', '>', now());
    }

    public function scopeSent($query)
    {
        return $query->whereNotNull('sent_at');
    }

    public function scopePending($query)
    {
        return $query->whereNull('sent_at');
    }

    // Helper methods
    public function markAsSent()
    {
        $this->update([
            'sent_at' => now(),
        ]);
    }

    public function getStatusAttribute()
    {
        if (!$this->is_active) return 'Inactive';
        if ($this->sent_at) return 'Sent';
        if ($this->scheduled_at && $this->scheduled_at->isFuture()) return 'Scheduled';
        return 'Pending';
    }

    public function getStatusClassAttribute()
    {
        return [
            'Sent' => 'bg-green-500',
            'Scheduled' => 'bg-blue-500',
            'Pending' => 'bg-yellow-500',
            'Inactive' => 'bg-gray-500',
        ][$this->status] ?? 'bg-gray-500';
    }
}