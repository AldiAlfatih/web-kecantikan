<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');              // Nama produk
            $table->string('brand');             // Brand
            $table->string('category');          // Foundation, Lipstick
            $table->integer('price');            // Harga
            $table->integer('stock');            // Stok
            $table->enum('skin_type', ['normal','berminyak','kering','kombinasi','sensitif']);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }

};
