
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan status ketersediaan stok pada setiap menu.
     */
    public function up(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            // Status stok otomatis dimulai dari Tersedia.
            $table->string('status_stok')
                  ->default('Tersedia')
                  ->after('status');
        });
    }

    /**
     * Menghapus kolom status_stok jika migration dibatalkan.
     */
    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            // Menghapus kolom yang ditambahkan pada up().
            $table->dropColumn('status_stok');
        });
    }
};
