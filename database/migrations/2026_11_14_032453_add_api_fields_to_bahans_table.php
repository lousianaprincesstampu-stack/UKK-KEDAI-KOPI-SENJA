<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bahans', function (Blueprint $table) {

            // Kode unik bahan dari layanan API UKK.
            $table->string('kode_api')->nullable()->unique();
            $table->decimal('harga_per_satuan', 12, 2)->nullable();

            // Tanggal terakhir harga bahan diperbarui dari API.
            $table->date('harga_diperbarui')->nullable();
        });
    }

    public function down(): void
    {

        Schema::table('bahans', function (Blueprint $table) {
            $table->dropUnique(['kode_api']);
            $table->dropColumn([
                'kode_api',
                'harga_per_satuan',
                'harga_diperbarui',
            ]);
        });
    }
};
