<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menus', function (Blueprint $table) {

            $table->string('kode_menu')->nullable()->unique()->after('id');

            $table->string('kategori')->nullable()->after('nama_menu');
        });
    }

    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {

            $table->dropUnique(['kode_menu']);

            $table->dropColumn(['kode_menu', 'kategori']);
        });
    }
};
