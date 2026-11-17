
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recipe_details', function (Blueprint $table) {

            $table->foreignId('recipe_id')
                ->constrained('recipes')
                ->cascadeOnDelete();

            $table->foreignId('bahan_id')
                ->constrained('bahans')
                ->restrictOnDelete();

            $table->decimal('jumlah', 12, 3);

            $table->unique(['recipe_id', 'bahan_id']);
        });

        Schema::table('cafe_tables', function (Blueprint $table) {

            $table->string('nomor_meja')->unique();
        });
    }

    public function down(): void
    {
        Schema::table('recipe_details', function (Blueprint $table) {

            $table->dropUnique(['recipe_id', 'bahan_id']);

            $table->dropConstrainedForeignId('recipe_id');
            $table->dropConstrainedForeignId('bahan_id');

            $table->dropColumn('jumlah');
        });

        Schema::table('cafe_tables', function (Blueprint $table) {

            $table->dropUnique(['nomor_meja']);
            $table->dropColumn('nomor_meja');
        });
    }
};
