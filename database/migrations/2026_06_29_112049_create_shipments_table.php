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
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('courier_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('nomor_resi')->unique()->nullable();
            $table->string('alamat_pengiriman');
            $table->string('jasa_ekspedisi');
            $table->enum('status_pengiriman', ['manifest', 'dalam_proses', 'sedang_dikirim', 'sampai_tujuan', 'gagal_kirim'])->default('manifest');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
