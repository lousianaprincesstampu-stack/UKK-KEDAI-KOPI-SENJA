@extends('manager.layouts.app')
@section('content')

<div class="mb-4">
    <h4>Edit Supplier</h4>
    <p class="text-muted mb-0">Perbarui informasi supplier.</p>
</div>

{{-- Form edit supplier --}}
<div class="card border-0 shadow-sm">
    <div class="card-body">

        <form action="{{ route('manager.suppliers.update', $supplier->id) }}"
              method="POST">

            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Nama Supplier</label>
                <input type="text"
                       name="nama_supplier"
                       class="form-control @error('nama_supplier') is-invalid @enderror"
                       value="{{ old('nama_supplier', $supplier->nama_supplier) }}"
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
                       value="{{ old('no_telepon', $supplier->no_telepon) }}">

                @error('no_telepon')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Alamat</label>
                <textarea name="alamat"
                          class="form-control @error('alamat') is-invalid @enderror"
                          rows="3">{{ old('alamat', $supplier->alamat) }}</textarea>

                @error('alamat')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan Perubahan
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
