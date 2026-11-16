
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('nama_supplier');
            $table->text('alamat')->nullable();
            $table->string('no_telepon')->nullable();
            $table->string('api_url')->nullable();
            $table->text('api_key')->nullable();
            $table->boolean('status')->default(true);
        });
    }

    // Menghapus kolom jika migration dibatalkan.
    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn([
                'nama_supplier',
                'alamat',
                'no_telepon',
                'api_url',
                'api_key',
                'status',
            ]);
        });
    }
};
