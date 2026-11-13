
<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {

        Schema::table('supplier_prices', function (Blueprint $table) {

            $table->renameColumn('tanggal harga', 'tanggal_harga');
        });

        Schema::table('supplier_prices', function (Blueprint $table) {

            $table->dropForeign(['supplier_id']);

            $table->dropForeign(['bahan_id']);
        });

        Schema::table('supplier_prices', function (Blueprint $table) {

            $table->foreign('supplier_id')
                ->references('id')
                ->on('suppliers')
                ->restrictOnDelete();

            $table->foreign('bahan_id')
                ->references('id')
                ->on('bahans')
                ->restrictOnDelete();

            $table->index(
                ['supplier_id', 'bahan_id', 'tanggal_harga'],
                'supplier_prices_history_index'
            );
        });

        Schema::table('stock_movements', function (Blueprint $table) {

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
        });
    }


    public function down(): void
    {

        Schema::table('stock_movements', function (Blueprint $table) {

            $table->dropConstrainedForeignId('user_id');
        });

        Schema::table('supplier_prices', function (Blueprint $table) {

            $table->dropIndex('supplier_prices_history_index');
        });

        Schema::table('supplier_prices', function (Blueprint $table) {

            $table->dropForeign(['supplier_id']);
            $table->dropForeign(['bahan_id']);

            $table->foreign('supplier_id')
                ->references('id')
                ->on('suppliers')
                ->cascadeOnDelete();

            $table->foreign('bahan_id')
                ->references('id')
                ->on('bahans')
                ->cascadeOnDelete();
        });


        Schema::table('supplier_prices', function (Blueprint $table) {

            $table->renameColumn('tanggal_harga', 'tanggal harga');
        });
    }
};
