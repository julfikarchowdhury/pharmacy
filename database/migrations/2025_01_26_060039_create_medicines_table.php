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
        Schema::create('medicines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();
            $table->foreignId('medicine_company_id')->constrained('medicine_companies')->restrictOnDelete();
            $table->foreignId('medicine_generic_id')->constrained('medicine_generics')->restrictOnDelete();
            $table->foreignId('concentration_id')->constrained('concentrations')->restrictOnDelete();
            $table->string('name_en');
            $table->string('name_bn');
            $table->text('description_en');
            $table->text('description_bn');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('strip_price', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicines');
    }
};
