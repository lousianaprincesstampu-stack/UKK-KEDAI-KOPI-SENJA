@extends('manager.layouts.app')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Data Supplier</h4>

    <a href="{{ route('manager.suppliers.create') }}"
       class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Tambah Supplier
    </a>
</div>

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

{{-- Form pencarian --}}
<form action="{{ route('manager.suppliers.index') }}"
      method="GET"
      class="mb-3">

    <div class="input-group">
        <input type="text"
               name="cari"
               class="form-control"
               placeholder="Cari nama supplier..."
               value="{{ $cari }}">

        <button type="submit" class="btn btn-primary">
            Cari
        </button>

        @if ($cari)
            <a href="{{ route('manager.suppliers.index') }}"
               class="btn btn-secondary">
                Reset
            </a>
        @endif
    </div>
</form>

{{-- Tabel data supplier --}}
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Nama Supplier</th>
                    <th>Alamat</th>
                    <th>No. Telepon</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($suppliers as $supplier)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $supplier->nama_supplier }}</td>
                        <td>{{ $supplier->alamat ?? '-' }}</td>
                        <td>{{ $supplier->no_telepon ?? '-' }}</td>

                        <td>
                            @if ($supplier->status)
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-secondary">Nonaktif</span>
                            @endif
                        </td>

                        <td>
                            {{-- Tombol edit --}}
                            <a href="{{ route('manager.suppliers.edit', $supplier->id) }}"
                               class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil-square"></i>
                            </a>

                            {{-- Tombol hapus --}}
                            <form action="{{ route('manager.suppliers.destroy', $supplier->id) }}"
                                  method="POST"
                                  class="d-inline"
                                  onsubmit="return confirm('Yakin ingin menghapus supplier ini?')">

                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                        class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            Belum ada data supplier.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
