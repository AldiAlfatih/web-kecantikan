<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('order_code')->unique();

            $table->unsignedInteger('total_price')->default(0);

            // status order sesuai yang kamu pakai di Filament
            $table->enum('status', [
                'menunggu_verifikasi',
                'diproses',
                'dikirim',
                'selesai',
                'dibatalkan',
            ])->default('menunggu_verifikasi');

            // shipping / receiver fields (langsung taruh di create, tanpa after())
            $table->string('receiver_name', 120)->nullable();
            $table->string('receiver_email', 120)->nullable();
            $table->string('receiver_phone', 30)->nullable();

            $table->text('shipping_address')->nullable();
            $table->enum('delivery_method', ['courier', 'pickup'])->default('courier');
            $table->string('shipping_note', 255)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};