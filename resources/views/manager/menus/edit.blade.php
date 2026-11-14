@extends('manager.layouts.app')
@section('title', 'Edit Menu - Kedai Kopi Senja')
@section('content')

    <div class="mb-4">
        <h3 class="fw-bold">Edit Menu</h3>
        <p class="text-muted">Ubah informasi menu yang dipilih.</p>
    </div>

    <div class="table-container">

        {{-- Menampilkan pesan kesalahan validasi --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('manager.menus.update', $menu->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="nama_menu" class="form-label">Nama Menu</label>
                <input
                    type="text"
                    class="form-control"
                    id="nama_menu"
                    name="nama_menu"
                    value="{{ old('nama_menu', $menu->nama_menu) }}"
                    required
                >
            </div>

            <div class="mb-3">
                <label for="harga_jual" class="form-label">Harga Jual</label>
                <input
                    type="number"
                    class="form-control"
                    id="harga_jual"
                    name="harga_jual"
                    value="{{ old('harga_jual', $menu->harga_jual) }}"
                    min="1"
                    required
                >
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan Perubahan
                </button>

                <a href="{{ route('manager.menus.index') }}"
                   class="btn btn-secondary">
                    Kembali
                </a>
            </div>

        </form>
    </div>

@endsection
