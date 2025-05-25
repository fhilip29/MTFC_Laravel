<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSettings extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'about_us_content',
        'community_content',
        'our_values',
        'address_line1',
        'address_line2',
        'phone_number',
        'email',
        'working_hours_weekday',
        'working_hours_weekend',
        'facebook_url',
        'instagram_url',
        'twitter_url',
        'youtube_url',
        'google_maps_embed_url',
        'location_section_title',
        'location_section_description',
        'about_address_line1',
        'about_address_line2',
        'about_phone_number',
        'about_email',
        'about_working_hours_weekday',
        'about_working_hours_weekend',
        'about_google_maps_embed_url',
    ];
    
    protected $casts = [
        'our_values' => 'array',
    ];
    
    /**
     * Get the default site settings or create if not exists
     */
    public static function getSettings()
    {
        $settings = self::first();
        
        if (!$settings) {
            $settings = self::create([
                'our_values' => json_encode([
                    'Integrity in all we do',
                    'Excellence in service',
                    'Community support',
                    'Results-driven approach'
                ])
            ]);
        }
        
        return $settings;
    }
}
