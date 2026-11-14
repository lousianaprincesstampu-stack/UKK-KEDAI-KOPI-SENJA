@extends('manager.layouts.app')

@section('title', 'Tambah Menu - Kedai Kopi Senja')

@section('content')


    <div class="mb-4">
        <h3 class="fw-bold">Tambah Menu</h3>

        <p class="text-muted">
            Masukkan informasi menu baru.
        </p>
    </div>

    <div class="table-container">

        <form action="#" method="POST">
            @csrf

            <div class="mb-3">
                <label for="nama_menu" class="form-label">
                    Nama Menu
                </label>

                <input
                    type="text"
                    class="form-control"
                    id="nama_menu"
                    name="nama_menu"
                    placeholder="Contoh: Es Kopi Susu"
                    required
                >
            </div>

            <div class="mb-3">
                <label for="harga_jual" class="form-label">
                    Harga Jual
                </label>

                <input
                    type="number"
                    class="form-control"
                    id="harga_jual"
                    name="harga_jual"
                    placeholder="Contoh: 15000"
                    min="1"
                    required
                >
            </div>

            <div class="d-flex gap-2">

                <button type="submit" class="btn btn-primary" disabled>
                    <i class="bi bi-save"></i> Simpan Menu
                </button>

                <a href="{{ route('manager.menus.index') }}"
                   class="btn btn-secondary">
                    Kembali
                </a>

            </div>

        </form>

    </div>

@endsection
