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
        Schema::create('pricing_plans', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // gym, boxing, muay, jiu-jitsu
            $table->string('plan'); // monthly, daily, per-session
            $table->decimal('price', 10, 2);
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('features')->nullable(); // Array of features/inclusions
            $table->boolean('is_active')->default(true);
            $table->integer('display_order')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_promo')->default(false);
            $table->date('promo_ends_at')->nullable();
            $table->decimal('original_price', 10, 2)->nullable(); // Original price before promo
            $table->timestamps();
            
            // Add unique constraint to prevent duplicates
            $table->unique(['type', 'plan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricing_plans');
    }
}; 