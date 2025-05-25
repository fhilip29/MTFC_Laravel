<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            
            // About Page Content
            $table->text('about_us_content')->nullable();
            $table->text('community_content')->nullable();
            $table->json('our_values')->nullable();
            
            // Contact Information
            $table->string('address_line1')->nullable();
            $table->string('address_line2')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
            $table->string('working_hours_weekday')->nullable();
            $table->string('working_hours_weekend')->nullable();
            
            // Social Media
            $table->string('facebook_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('twitter_url')->nullable();
            $table->string('youtube_url')->nullable();
            
            // Google Maps
            $table->text('google_maps_embed_url')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
