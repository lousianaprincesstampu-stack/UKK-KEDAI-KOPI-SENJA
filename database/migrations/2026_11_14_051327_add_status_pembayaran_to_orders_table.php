<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Mengembalikan kolom-kolom tabel customers.
        Schema::table('customers', function (Blueprint $table) {
            $table->string('nama');
            $table->string('no_hp')->unique();
            $table->string('password');
            $table->boolean('must_change_password')->default(false);
        });

        // 2. Mengembalikan kolom-kolom tabel orders.
        Schema::table('orders', function (Blueprint $table) {
            // Identitas pesanan.
            $table->string('kode_pesanan')->unique();

            // Relasi dengan pelanggan dan kasir.
            $table->foreignId('customer_id')
                ->nullable()
                ->constrained('customers')
                ->restrictOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->restrictOnDelete();

            // Informasi transaksi.
            $table->dateTime('tanggal_pesanan');
            $table->string('status_pesanan')->default('Baru');

            // Informasi pembayaran.
            $table->string('status_pembayaran')
                ->default('Belum Dibayar');

            $table->string('metode_pembayaran')->nullable();

            // Rincian perhitungan harga.
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('service', 12, 2)->default(0);
            $table->decimal('pajak', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
        });

        // 3. Mengembalikan kolom-kolom tabel order_details.
        Schema::table('order_details', function (Blueprint $table) {
            // Relasi dengan pesanan.
            $table->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnDelete();

            // Relasi dengan menu.
            $table->foreignId('menu_id')
                ->nullable()
                ->constrained('menus')
                ->nullOnDelete();

            // Rincian menu yang dipesan.
            $table->string('nama_menu');
            $table->unsignedInteger('jumlah');
            $table->decimal('harga', 12, 2);
            $table->decimal('hpp', 12, 2)->default(0);
            $table->decimal('subtotal', 12, 2);
        });
    }

    public function down(): void
    {
        // Menghapus relasi dan kolom order_details.
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropConstrainedForeignId('order_id');
            $table->dropConstrainedForeignId('menu_id');

            $table->dropColumn([
                'nama_menu',
                'jumlah',
                'harga',
                'hpp',
                'subtotal',
            ]);
        });

        // Menghapus relasi dan kolom orders.
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('customer_id');
            $table->dropConstrainedForeignId('user_id');

            $table->dropUnique(['kode_pesanan']);

            $table->dropColumn([
                'kode_pesanan',
                'tanggal_pesanan',
                'status_pesanan',
                'status_pembayaran',
                'metode_pembayaran',
                'subtotal',
                'service',
                'pajak',
                'total',
            ]);
        });

        // Menghapus kolom customers.
        Schema::table('customers', function (Blueprint $table) {
            $table->dropUnique(['no_hp']);

            $table->dropColumn([
                'nama',
                'no_hp',
                'password',
                'must_change_password',
            ]);
        });
    }
};
