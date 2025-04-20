<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('orders', function (Blueprint $table) {
        $table->id();
        $table->string('reference_no')->unique();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        
        // Delivery Info
        $table->string('first_name');
        $table->string('last_name');
        $table->string('street');
        $table->string('barangay');
        $table->string('city');
        $table->string('postal_code');
        $table->string('phone_number');

        // Order Info
        $table->string('payment_method'); // cod, gcash, etc.
        $table->enum('status', ['Pending', 'Accepted', 'Out for Delivery', 'Completed', 'Cancelled'])->default('Pending');
        $table->date('order_date')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
