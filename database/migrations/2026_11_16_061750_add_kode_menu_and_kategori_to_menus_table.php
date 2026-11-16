<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menus', function (Blueprint $table) {

            // Menyimpan kode menu dari CSV, boleh kosong untuk data lama.
            $table->string('kode_menu')->nullable()->unique()->after('id');

            // Menyimpan kategori menu, misalnya Minuman atau Makanan.
            $table->string('kategori')->nullable()->after('nama_menu');
        });
    }

    // Mengembalikan struktur tabel seperti semula jika migration di-rollback.
    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {

            // Menghapus indeks unik kode menu terlebih dahulu.
            $table->dropUnique(['kode_menu']);

            $table->dropColumn(['kode_menu', 'kategori']);
        });
    }
};
