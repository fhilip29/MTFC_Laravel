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
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_promo')->default(false)->after('status');
            $table->decimal('original_price', 10, 2)->nullable()->after('is_promo');
            $table->timestamp('promo_ends_at')->nullable()->after('original_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['is_promo', 'original_price', 'promo_ends_at']);
        });
    }
};
