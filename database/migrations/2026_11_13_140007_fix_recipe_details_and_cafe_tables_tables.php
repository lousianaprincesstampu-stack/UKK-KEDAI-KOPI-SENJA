
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Memperbaiki tabel recipe_details
        Schema::table('recipe_details', function (Blueprint $table) {

            // Menghubungkan detail resep dengan tabel recipes
            $table->foreignId('recipe_id')
                ->constrained('recipes')
                ->cascadeOnDelete();

            // Menghubungkan detail resep dengan tabel bahans
            $table->foreignId('bahan_id')
                ->constrained('bahans')
                ->restrictOnDelete();

            // Menyimpan jumlah bahan yang dibutuhkan
            $table->decimal('jumlah', 12, 3);

            // Mencegah bahan yang sama tercatat dua kali dalam satu resep
            $table->unique(['recipe_id', 'bahan_id']);
        });

        // Memperbaiki tabel cafe_tables
        Schema::table('cafe_tables', function (Blueprint $table) {

            // Menambahkan nomor meja yang harus unik
            $table->string('nomor_meja')->unique();
        });
    }

    public function down(): void
    {
        // Membatalkan perubahan pada tabel recipe_details
        Schema::table('recipe_details', function (Blueprint $table) {

            // Menghapus indeks unik terlebih dahulu
            $table->dropUnique(['recipe_id', 'bahan_id']);

            // Menghapus foreign key dan kolom terkait
            $table->dropConstrainedForeignId('recipe_id');
            $table->dropConstrainedForeignId('bahan_id');

            // Menghapus kolom jumlah
            $table->dropColumn('jumlah');
        });

        // Membatalkan perubahan pada tabel cafe_tables
        Schema::table('cafe_tables', function (Blueprint $table) {

            // Menghapus indeks unik dan kolom nomor meja
            $table->dropUnique(['nomor_meja']);
            $table->dropColumn('nomor_meja');
        });
    }
};
