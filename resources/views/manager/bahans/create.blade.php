@extends('manager.layouts.app')
@section('content')

<div class="container-fluid">

    {{-- Judul halaman --}}
    <div class="mb-4">
        <h3>Tambah Bahan</h3>
        <p class="text-muted">Tambahkan data bahan baru ke dalam sistem.</p>
    </div>

    {{-- Form tambah bahan --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body">

            <form action="{{ route('manager.bahans.store') }}" method="POST">
                @csrf

                {{-- Nama bahan --}}
                <div class="mb-3">
                    <label for="nama_bahan" class="form-label">Nama Bahan</label>
                    <input type="text"
                           name="nama_bahan"
                           id="nama_bahan"
                           class="form-control @error('nama_bahan') is-invalid @enderror"
                           value="{{ old('nama_bahan') }}"
                           placeholder="Contoh: Susu UHT">

                    @error('nama_bahan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Kategori bahan --}}
                <div class="mb-3">
                    <label for="kategori" class="form-label">Kategori</label>
                    <input type="text"
                           name="kategori"
                           id="kategori"
                           class="form-control"
                           value="{{ old('kategori') }}"
                           placeholder="Contoh: Dairy">
                </div>

                {{-- Satuan dasar --}}
                <div class="mb-3">
                    <label for="satuan_dasar" class="form-label">Satuan Dasar</label>
                    <select name="satuan_dasar"
                            id="satuan_dasar"
                            class="form-select @error('satuan_dasar') is-invalid @enderror">

                        <option value="">-- Pilih Satuan --</option>
                        <option value="g" {{ old('satuan_dasar') == 'g' ? 'selected' : '' }}>Gram (g)</option>
                        <option value="ml" {{ old('satuan_dasar') == 'ml' ? 'selected' : '' }}>Mililiter (ml)</option>
                        <option value="pcs" {{ old('satuan_dasar') == 'pcs' ? 'selected' : '' }}>Pieces (pcs)</option>
                    </select>

                    @error('satuan_dasar')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Stok awal --}}
                <div class="mb-3">
                    <label for="stok" class="form-label">Stok Awal</label>
                    <input type="number"
                           name="stok"
                           id="stok"
                           class="form-control @error('stok') is-invalid @enderror"
                           value="{{ old('stok', 0) }}"
                           min="0"
                           step="0.001">

                    @error('stok')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Stok minimum --}}
                <div class="mb-3">
                    <label for="stok_minimum" class="form-label">Stok Minimum</label>
                    <input type="number"
                           name="stok_minimum"
                           id="stok_minimum"
                           class="form-control @error('stok_minimum') is-invalid @enderror"
                           value="{{ old('stok_minimum', 0) }}"
                           min="0"
                           step="0.001">

                    @error('stok_minimum')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan Bahan
                    </button>

                    <a href="{{ route('manager.bahans.index') }}"
                       class="btn btn-secondary">
                        Kembali
                    </a>
                </div>

            </form>

        </div>
    </div>

</div>

@endsection
