<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Message extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sender_id',
        'recipient_id',
        'subject',
        'content',
        'is_read',
        'read_at',
        'parent_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    /**
     * Get the sender of the message.
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the recipient of the message.
     */
    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }
    
    /**
     * Get the parent message.
     */
    public function parent()
    {
        return $this->belongsTo(Message::class, 'parent_id');
    }
    
    /**
     * Get the replies to this message.
     */
    public function replies()
    {
        return $this->hasMany(Message::class, 'parent_id');
    }
    
    /**
     * Format the created_at attribute.
     *
     * @return string
     */
    public function getFormattedCreatedAtAttribute()
    {
        return Carbon::parse($this->created_at)->format('M d, Y h:i A');
    }
    
    /**
     * Format the read_at attribute.
     *
     * @return string|null
     */
    public function getFormattedReadAtAttribute()
    {
        return $this->read_at ? Carbon::parse($this->read_at)->format('M d, Y h:i A') : null;
    }
    
    /**
     * Check if the message was created recently.
     *
     * @return bool
     */
    public function getIsRecentAttribute()
    {
        return $this->created_at->gt(Carbon::now()->subDays(3));
    }
} 