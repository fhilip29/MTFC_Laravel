<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricingPlan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type',
        'plan',
        'price',
        'name',
        'description',
        'features',
        'is_active',
        'display_order',
        'is_featured',
        'is_promo',
        'promo_ends_at',
        'original_price'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'float',
        'original_price' => 'float',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_promo' => 'boolean',
        'features' => 'array',
        'display_order' => 'integer',
        'promo_ends_at' => 'date',
    ];

    /**
     * Get the sport that this plan belongs to
     */
    public function sport()
    {
        return $this->belongsTo(Sport::class, 'type', 'slug');
    }

    /**
     * Scope a query to only include active plans.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include plans of a specific type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }
    
    /**
     * Check if the plan is currently on promotion
     * 
     * @return bool
     */
    public function isOnPromo()
    {
        return $this->is_promo && (!$this->promo_ends_at || $this->promo_ends_at->isFuture());
    }
    
    /**
     * Get the price to display (considering promos)
     * 
     * @return float
     */
    public function getDisplayPrice()
    {
        if ($this->isOnPromo() && $this->original_price) {
            return $this->price;
        }
        
        return $this->price;
    }
    
    /**
     * Seed initial data from existing plans
     */
    public static function seedInitialData()
    {
        $defaultPlans = [
            // Gym plans
            [
                'type' => 'gym',
                'plan' => 'monthly',
                'price' => 1000.00,
                'name' => 'Monthly Membership',
                'features' => [
                    'Unlimited gym access',
                    'Shower Room Access',
                    'Access to group fitness classes'
                ],
                'is_active' => true,
                'display_order' => 1,
                'is_featured' => true
            ],
            [
                'type' => 'gym',
                'plan' => 'daily',
                'price' => 100.00,
                'name' => 'Daily Pass',
                'features' => [
                    'Full gym access for one day',
                    'Access to locker rooms',
                    'Towel service included'
                ],
                'is_active' => true,
                'display_order' => 2
            ],
            
            // Boxing plans
            [
                'type' => 'boxing',
                'plan' => 'monthly',
                'price' => 3000.00,
                'name' => 'Monthly Membership',
                'features' => [
                    'Free Use of Gym',
                    'Shower Given',
                    'Free Use of Boxing Equipment'
                ],
                'is_active' => true,
                'display_order' => 1,
                'is_featured' => true
            ],
            [
                'type' => 'boxing',
                'plan' => 'per-session',
                'price' => 260.00,
                'name' => 'Per Session',
                'features' => [
                    'One boxing class',
                    'Sessions tracked in your account',
                    'Free use of gym for the day',
                    'Shower access',
                    'Boxing equipment provided'
                ],
                'is_active' => true,
                'display_order' => 2
            ],
            
            // Muay Thai plans
            [
                'type' => 'muay',
                'plan' => 'monthly',
                'price' => 2600.00,
                'name' => 'Monthly Membership',
                'features' => [
                    'Free Use of Gym',
                    'Shower Given',
                    'Free Use of Boxing Equipment'
                ],
                'is_active' => true,
                'display_order' => 1,
                'is_featured' => true
            ],
            [
                'type' => 'muay',
                'plan' => 'per-session',
                'price' => 350.00,
                'name' => 'Per Session',
                'features' => [
                    'One Muay Thai class',
                    'Sessions tracked in your account',
                    'Free use of gym for the day',
                    'Shower access',
                    'Muay Thai equipment provided'
                ],
                'is_active' => true,
                'display_order' => 2
            ],
            
            // Jiu-jitsu plans
            [
                'type' => 'jiu',
                'plan' => 'monthly',
                'price' => 3500.00,
                'name' => 'Monthly Membership',
                'features' => [
                    'Unlimited Jiu Jitsu classes',
                    'Belt promotion eligibility',
                    'Open mat access',
                    'Full gym access included'
                ],
                'is_active' => true,
                'display_order' => 1,
                'is_featured' => true
            ],
            [
                'type' => 'jiu',
                'plan' => 'per-session',
                'price' => 400.00,
                'name' => 'Per-Session Pass',
                'features' => [
                    'Single Jiu Jitsu session',
                    'Sessions tracked in your account',
                    'Gi rental available',
                    'Free use of gym for the day',
                    'Shower access'
                ],
                'is_active' => true,
                'display_order' => 2
            ]
        ];
        
        foreach ($defaultPlans as $plan) {
            self::updateOrCreate(
                ['type' => $plan['type'], 'plan' => $plan['plan']],
                $plan
            );
        }
    }
} 