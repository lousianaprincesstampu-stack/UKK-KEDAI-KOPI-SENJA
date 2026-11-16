<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bahans', function (Blueprint $table) {
            
            // Dibuat nullable agar data bahan lama tetap aman.
            $table->string('kode_bahan')->nullable()->unique()->after('nama_bahan');

        });
    }

    public function down(): void
    {
        Schema::table('bahans', function (Blueprint $table) {

            // Menghapus kode bahan jika migration dibatalkan.
            $table->dropUnique(['kode_bahan']);
            $table->dropColumn('kode_bahan');

        });
    }
};
