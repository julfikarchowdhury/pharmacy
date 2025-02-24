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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_id')->unique();
            $table->foreignId('customer_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('pharmacy_id')->constrained('pharmacies')->restrictOnDelete();
            $table->decimal('total', 10, 2);
            $table->decimal('sub_total', 10, 2);
            $table->string('delivery_address');
            $table->double('delivery_lat');
            $table->double('delivery_long');
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
            $table->double('delivery_charge');
            $table->double('discount_by_points');
            $table->double('pharmacy_discount');
            $table->double('tax');
            $table->timestamp('date');
            $table->enum('payment_type', ['cod', 'bkash','nagad'])->default('cod');
            $table->enum('payment_status', ['paid', 'due'])->default('due');
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
