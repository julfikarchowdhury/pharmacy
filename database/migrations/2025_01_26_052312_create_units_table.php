<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create the table
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('value_en')->unique();
            $table->string('value_bn')->unique();
            $table->timestamps();
        });

        //as there was a compelxity regarding the unit of the medicines that why that manual apporch is taken..
        //on app side the condition is based on either id or value.so it shouldnot be chnaged before commiunicate with app developer
        // Insert default data for 'pc' and 'strip'
        DB::table('units')->insert([
            [
                'value_en' => 'pc',
                'value_bn' => 'পিস',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'value_en' => 'strip',
                'value_bn' => 'স্ট্রিপ',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
