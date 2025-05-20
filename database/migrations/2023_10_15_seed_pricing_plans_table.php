<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\PricingPlan;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Call the seedInitialData method on the PricingPlan model
        PricingPlan::seedInitialData();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Clear all pricing plans
        \DB::table('pricing_plans')->truncate();
    }
}; 