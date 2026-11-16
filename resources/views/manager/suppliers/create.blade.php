@extends('manager.layouts.app')
@section('content')

<div class="mb-4">
    <h4>Tambah Supplier</h4>
    <p class="text-muted mb-0">Masukkan informasi supplier baru.</p>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">

        <form action="{{ route('manager.suppliers.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label">Nama Supplier</label>
                <input type="text"
                       name="nama_supplier"
                       class="form-control @error('nama_supplier') is-invalid @enderror"
                       value="{{ old('nama_supplier') }}"
                       placeholder="Masukkan nama supplier"
                       required>

                @error('nama_supplier')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Nomor Telepon</label>
                <input type="text"
                       name="no_telepon"
                       class="form-control @error('no_telepon') is-invalid @enderror"
                       value="{{ old('no_telepon') }}"
                       placeholder="Masukkan nomor telepon">

                @error('no_telepon')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Alamat</label>
                <textarea name="alamat"
                          class="form-control @error('alamat') is-invalid @enderror"
                          rows="3"
                          placeholder="Masukkan alamat supplier">{{ old('alamat') }}</textarea>

                @error('alamat')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan
                </button>

                <a href="{{ route('manager.suppliers.index') }}"
                   class="btn btn-secondary">
                    Kembali
                </a>
            </div>

        </form>
    </div>
</div>

@endsection
