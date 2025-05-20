<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sport extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'background_image',
        'short_description',
        'is_active',
        'display_order'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'display_order' => 'integer'
    ];

    /**
     * Get the pricing plans associated with the sport
     */
    public function pricingPlans()
    {
        return $this->hasMany(PricingPlan::class, 'type', 'slug');
    }
    
    /**
     * Get only active plans for this sport
     */
    public function activePlans()
    {
        return $this->pricingPlans()->where('is_active', true)->orderBy('display_order');
    }
    
    /**
     * Get the URL for pricing page
     */
    public function getPricingUrl()
    {
        return route('pricing.show', $this->slug);
    }
    
    /**
     * Scope a query to only include active sports.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    /**
     * Get the trainers who specialize in this sport
     */
    public function trainers()
    {
        return $this->belongsToMany(User::class, 'trainer_specialties', 'sport_id', 'user_id')
            ->where('role', 'trainer');
    }
} 