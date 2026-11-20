
@extends('manager.layouts.app')

@section('content')
<div class="container-fluid">

    <div class="mb-4">
        <h3 class="fw-bold">Tambah Bahan</h3>
        <p class="text-muted">Tambahkan data bahan baru.</p>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">

            <form action="{{ route('manager.bahans.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="nama_bahan" class="form-label">Nama Bahan</label>
                    <input type="text" name="nama_bahan" id="nama_bahan"
                        class="form-control @error('nama_bahan') is-invalid @enderror"
                        value="{{ old('nama_bahan') }}">

                    @error('nama_bahan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="kategori" class="form-label">Kategori</label>
                    <input type="text" name="kategori" id="kategori"
                        class="form-control"
                        value="{{ old('kategori') }}">
                </div>

                <div class="mb-3">
                    <label for="satuan_dasar" class="form-label">Satuan Dasar</label>
                    <select name="satuan_dasar" id="satuan_dasar" class="form-select">
                        <option value="">-- Pilih Satuan --</option>
                        <option value="g" {{ old('satuan_dasar') == 'g' ? 'selected' : '' }}>Gram (g)</option>
                        <option value="ml" {{ old('satuan_dasar') == 'ml' ? 'selected' : '' }}>Mililiter (ml)</option>
                        <option value="pcs" {{ old('satuan_dasar') == 'pcs' ? 'selected' : '' }}>Pieces (pcs)</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="stok" class="form-label">Stok</label>
                    <input type="number" name="stok" id="stok"
                        class="form-control"
                        min="0" step="0.001"
                        value="{{ old('stok', 0) }}">
                </div>

                <div class="mb-3">
                    <label for="stok_minimum" class="form-label">Stok Minimum</label>
                    <input type="number" name="stok_minimum" id="stok_minimum"
                        class="form-control"
                        min="0" step="0.001"
                        value="{{ old('stok_minimum', 0) }}">
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan
                    </button>

                    <a href="{{ route('manager.bahans.index') }}" class="btn btn-secondary">
                        Kembali
                    </a>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection
