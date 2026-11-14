
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Melengkapi tabel menus.
        Schema::table('menus', function (Blueprint $table) {
            $table->string('nama_menu')->unique();
            $table->decimal('harga_jual', 12, 2);
            $table->boolean('status')->default(true);
        });

        // Melengkapi tabel recipes.
        Schema::table('recipes', function (Blueprint $table) {
            $table->foreignId('menu_id')
                ->unique()
                ->constrained('menus')
                ->cascadeOnDelete();
        });

        // Melengkapi tabel stock_entries.
        Schema::table('stock_entries', function (Blueprint $table) {
            $table->foreignId('bahan_id')
                ->constrained('bahans')
                ->restrictOnDelete();

            $table->decimal('jumlah', 12, 3);
            $table->dateTime('tanggal');
            $table->string('keterangan')->nullable();

            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();
        });

        // Menambahkan batas minimum stok bahan, nilai awal 0.
        Schema::table('bahans', function (Blueprint $table) {
            $table->decimal('stok_minimum', 12, 3)->default(0);
        });
    }

    public function down(): void
    {
        // Menghapus kolom tambahan dari tabel bahans.
        Schema::table('bahans', function (Blueprint $table) {
            $table->dropColumn('stok_minimum');
        });

        // Menghapus relasi dan kolom dari stock_entries.
        Schema::table('stock_entries', function (Blueprint $table) {
            $table->dropConstrainedForeignId('bahan_id');
            $table->dropColumn(['jumlah', 'tanggal', 'keterangan']);
            $table->dropConstrainedForeignId('user_id');
        });

        // Menghapus relasi dan kolom dari recipes.
        Schema::table('recipes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('menu_id');
        });

        // Menghapus kolom dari menus.
        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn(['nama_menu', 'harga_jual', 'status']);
        });
    }
};
