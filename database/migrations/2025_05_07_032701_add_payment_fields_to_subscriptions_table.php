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
        Schema::table('subscriptions', function (Blueprint $table) {
            // Add amount column if it doesn't exist
            if (!Schema::hasColumn('subscriptions', 'amount')) {
                $table->decimal('amount', 10, 2)->nullable()->after('price');
            }
            
            // Add payment_method column if it doesn't exist
            if (!Schema::hasColumn('subscriptions', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('is_active');
            }
            
            // Add payment_status column if it doesn't exist
            if (!Schema::hasColumn('subscriptions', 'payment_status')) {
                $table->string('payment_status')->nullable()->after('payment_method');
            }
            
            // Add payment_reference column if it doesn't exist
            if (!Schema::hasColumn('subscriptions', 'payment_reference')) {
                $table->string('payment_reference')->nullable()->after('payment_status');
            }
            
            // Add waiver_accepted column if it doesn't exist
            if (!Schema::hasColumn('subscriptions', 'waiver_accepted')) {
                $table->boolean('waiver_accepted')->default(false)->after('payment_reference');
            }
            
            // Add cancelled_at column if it doesn't exist
            if (!Schema::hasColumn('subscriptions', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('waiver_accepted');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            // Drop columns if they exist
            $columns = ['amount', 'payment_method', 'payment_status', 'payment_reference', 'waiver_accepted', 'cancelled_at'];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('subscriptions', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
