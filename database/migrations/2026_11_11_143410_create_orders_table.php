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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->string('kode_pesanan')->unique();

             $table->foreignId('customer_id')
                ->nullable()
                ->constrained('customers')
                ->restrictOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->restrictOnDelete();

             $table->dateTime('tanggal_pesanan');
             $table->string('status_pesanan')->default('Baru');
             $table->string('metode_pembayaran')->nullable();
             $table->decimal('subtotal', 12, 2)->default(0);
             $table->decimal('service', 12, 2)->default(0);
              $table->decimal('pajak', 12, 2)->default(0);
              $table->decimal('total', 12, 2)->default(0);
              
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
