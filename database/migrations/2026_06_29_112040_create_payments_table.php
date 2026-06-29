<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->string('id_transaksi_gateway')->unique();
            $table->enum('metode_pembayaran', ['Bank', 'QRIS'])->nullable();
            $table->decimal('jumlah_dana', 12, 2);
            $table->enum('status_pembayaran', ['pending', 'sukses', 'gagal'])->default('pending');
            $table->timestamp('waktu_penyelesaian')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
