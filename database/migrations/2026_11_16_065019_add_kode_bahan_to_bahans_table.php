<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bahans', function (Blueprint $table) {
            
            $table->string('kode_bahan')->nullable()->unique()->after('nama_bahan');

        });
    }

    public function down(): void
    {
        Schema::table('bahans', function (Blueprint $table) {

            $table->dropUnique(['kode_bahan']);
            $table->dropColumn('kode_bahan');

        });
    }
};
