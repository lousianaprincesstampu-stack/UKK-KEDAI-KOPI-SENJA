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
        Schema::create('supplier_prices', function (Blueprint $table) {
            $table->id();

            $table->foreignId('supplier_id')
                ->constrained ('suppliers')
                ->restrictOnDelete();

            $table->foreignId('bahan_id')
                ->constrained ('bahans')
                ->restrictOnDelete();

                $table->decimal('harga', 12, 2);
                $table->dateTime('tanggal_harga');
                $table->index(['supplier_id', 'bahan_id', 'tanggal_harga']);


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_prices');
    }
};
