
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->string('nama_menu')->unique();
            $table->decimal('harga_jual', 12, 2);
            $table->boolean('status')->default(true);
        });

        Schema::table('recipes', function (Blueprint $table) {
            $table->foreignId('menu_id')
                ->unique()
                ->constrained('menus')
                ->cascadeOnDelete();
        });

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

        Schema::table('bahans', function (Blueprint $table) {
            $table->decimal('stok_minimum', 12, 3)->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('bahans', function (Blueprint $table) {
            $table->dropColumn('stok_minimum');
        });

        Schema::table('stock_entries', function (Blueprint $table) {
            $table->dropConstrainedForeignId('bahan_id');
            $table->dropColumn(['jumlah', 'tanggal', 'keterangan']);
            $table->dropConstrainedForeignId('user_id');
        });

        Schema::table('recipes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('menu_id');
        });

        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn(['nama_menu', 'harga_jual', 'status']);
        });
    }
};
