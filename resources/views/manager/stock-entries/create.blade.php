@extends('manager.layouts.app')
@section('title', 'Tambah Stok Masuk')
@section('content')

<div class="mb-4">
    <h4 class="fw-bold mb-1">Tambah Stok Masuk</h4>
    <p class="text-muted mb-0">Catat pembelian atau penambahan stok bahan.</p>
</div>

{{-- Formulir pencatatan stok masuk. --}}
<div class="card border-0 shadow-sm">
    <div class="card-body p-4">

        <form action="{{ route('manager.stock-entries.store') }}" method="POST">
            @csrf

            {{-- Memilih bahan yang akan ditambahkan stoknya.--}}
            <div class="mb-3">
                <label for="bahan_id" class="form-label fw-semibold">Nama Bahan</label>

                <select name="bahan_id" id="bahan_id"
                    class="form-select @error('bahan_id') is-invalid @enderror" required>

                    <option value="">-- Pilih Bahan --</option>

                    @foreach ($bahans as $bahan)
                        <option value="{{ $bahan->id }}"
                            {{ old('bahan_id') == $bahan->id ? 'selected' : '' }}>
                            {{ $bahan->nama_bahan }} ({{ $bahan->satuan_dasar }})
                        </option>
                    @endforeach
                </select>

                @error('bahan_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Mengisi jumlah bahan yang masuk. --}}
            <div class="mb-3">
                <label for="jumlah" class="form-label fw-semibold">Jumlah Stok Masuk</label>

                <input type="number" name="jumlah" id="jumlah"
                    class="form-control @error('jumlah') is-invalid @enderror"
                    value="{{ old('jumlah') }}"
                    min="0.001" step="0.001"
                    placeholder="Contoh: 5000" required>

                @error('jumlah')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                <small class="text-muted">
                    Masukkan jumlah sesuai satuan dasar bahan (g, ml, atau pcs).
                </small>
            </div>

            {{-- Mengisi tanggal pembelian atau penerimaan bahan. --}}
            <div class="mb-3">
                <label for="tanggal" class="form-label fw-semibold">Tanggal Stok Masuk</label>

                <input type="date" name="tanggal" id="tanggal"
                    class="form-control @error('tanggal') is-invalid @enderror"
                    value="{{ old('tanggal', now()->format('Y-m-d')) }}" required>

                @error('tanggal')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Keterangan tambahan bersifat opsional. --}}
            <div class="mb-4">
                <label for="keterangan" class="form-label fw-semibold">Keterangan</label>

                <textarea name="keterangan" id="keterangan" rows="3"
                    class="form-control @error('keterangan') is-invalid @enderror"
                    placeholder="Contoh: Pembelian bahan dari supplier">{{ old('keterangan') }}</textarea>

                @error('keterangan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan Stok Masuk
                </button>

                <a href="{{ route('manager.stock-entries.index') }}"
                    class="btn btn-secondary">
                    Kembali
                </a>
            </div>

        </form>
    </div>
</div>

@endsection
