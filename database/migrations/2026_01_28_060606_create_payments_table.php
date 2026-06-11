<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')->unique()->constrained('orders')->onDelete('cascade');

            $table->enum('method', ['transfer', 'cod', 'store']);
            $table->enum('status', ['pending', 'waiting_verification', 'verified', 'rejected'])
                  ->default('pending');

            $table->string('payment_proof')->nullable(); // path file bukti transfer
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete(); // admin

            $table->text('note')->nullable(); // alasan reject / catatan admin

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
