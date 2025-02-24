<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('manual_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('pharmacy_id')->constrained('pharmacies')->onDelete('cascade');
            $table->text('note')->nullable();
            $table->string('prescription')->nullable();
            $table->enum('status', [
                'order_placed',         // User places the order
                'store_accepts',        // Store accepts some products
                'store_rejects',        // Store rejects some products
                'ready_for_rider',      // Store prepares the product
                'rider_assigned',       // Rider is assigned to pick up the order
                'out_for_delivery',     // Rider is on the way to deliver
                'delivered',            // Order is delivered to the customer
                'canceled'              // Order is canceled
            ])->default('order_placed');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manual_orders');
    }
};
