@extends('manager.layouts.app')
@section('content')
<div class="container-fluid">

    <div class="mb-4">
        <h3 class="fw-bold">Edit Bahan</h3>
        <p class="text-muted">Perbarui seluruh informasi data bahan.</p>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">

            <form action="{{ route('manager.bahans.update', $bahan->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="nama_bahan" class="form-label">Nama Bahan</label>
                    <input type="text" name="nama_bahan" id="nama_bahan"
                        class="form-control @error('nama_bahan') is-invalid @enderror"
                        value="{{ old('nama_bahan', $bahan->nama_bahan) }}">

                    @error('nama_bahan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="kategori" class="form-label">Kategori</label>
                    <input type="text" name="kategori" id="kategori"
                        class="form-control"
                        value="{{ old('kategori', $bahan->kategori) }}">
                </div>

                <div class="mb-3">
                    <label for="satuan_dasar" class="form-label">Satuan Dasar</label>
                    <select name="satuan_dasar" id="satuan_dasar" class="form-select">
                        <option value="">-- Pilih Satuan --</option>
                        <option value="g" {{ old('satuan_dasar', $bahan->satuan_dasar) == 'g' ? 'selected' : '' }}>Gram (g)</option>
                        <option value="ml" {{ old('satuan_dasar', $bahan->satuan_dasar) == 'ml' ? 'selected' : '' }}>Mililiter (ml)</option>
                        <option value="pcs" {{ old('satuan_dasar', $bahan->satuan_dasar) == 'pcs' ? 'selected' : '' }}>Pieces (pcs)</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="stok" class="form-label">Stok</label>
                    <input type="number" name="stok" id="stok"
                        class="form-control"
                        min="0" step="0.001"
                        value="{{ old('stok', $bahan->stok) }}">
                </div>

                <div class="mb-3">
                    <label for="stok_minimum" class="form-label">Stok Minimum</label>
                    <input type="number" name="stok_minimum" id="stok_minimum"
                        class="form-control"
                        min="0" step="0.001"
                        value="{{ old('stok_minimum', $bahan->stok_minimum) }}">
                </div>

                <div class="mb-3">
                    <label for="harga_per_satuan" class="form-label">Harga per Satuan (Rp)</label>
                    <input type="number" name="harga_per_satuan" id="harga_per_satuan"
                        class="form-control"
                        min="0" step="0.01"
                        value="{{ old('harga_per_satuan', $bahan->harga_per_satuan) }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Kode API</label>
                    <input type="text" class="form-control"
                        value="{{ $bahan->kode_api ?? '-' }}" disabled>
                </div>

                <div class="mb-3">
                    <label class="form-label">Harga Diperbarui</label>
                    <input type="text" class="form-control"
                        value="{{ $bahan->harga_diperbarui ?? '-' }}" disabled>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan Perubahan
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
