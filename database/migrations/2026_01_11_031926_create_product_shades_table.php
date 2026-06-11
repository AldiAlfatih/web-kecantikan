<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('product_shades', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                  ->constrained('products')
                  ->onDelete('cascade');

            $table->string('shade_name');      // Warm Ivory, Cool Beige
            $table->string('tone');            // Fair, Medium, Deep
            $table->string('undertone');       // Warm, Cool, Neutral
            $table->integer('stock')->default(0);
            $table->string('hex_color');       // #F1D6C1
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_shades');
    }
};
