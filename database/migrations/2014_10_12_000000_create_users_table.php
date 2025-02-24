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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->unique();
            $table->string('email')->unique();
            $table->string('address');
            $table->double('lat')->nullable();
            $table->double('long')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->string('password');
            $table->integer('points')->default(0);
            $table->string('image');
            $table->enum('role', ['admin', 'user', 'pharmacy_owner']);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('device_token')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
