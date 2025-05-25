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
        Schema::table('site_settings', function (Blueprint $table) {
            // About page specific fields
            $table->string('about_address_line1')->nullable()->after('location_section_description');
            $table->string('about_address_line2')->nullable()->after('about_address_line1');
            $table->string('about_phone_number')->nullable()->after('about_address_line2');
            $table->string('about_email')->nullable()->after('about_phone_number');
            $table->string('about_working_hours_weekday')->nullable()->after('about_email');
            $table->string('about_working_hours_weekend')->nullable()->after('about_working_hours_weekday');
            $table->text('about_google_maps_embed_url')->nullable()->after('about_working_hours_weekend');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'about_address_line1',
                'about_address_line2',
                'about_phone_number',
                'about_email',
                'about_working_hours_weekday',
                'about_working_hours_weekend',
                'about_google_maps_embed_url'
            ]);
        });
    }
};
