@extends('cashier.layouts.app')
@section('title', 'Daftar Pesanan - Kedai Kopi Senja')
@section('content')

<div class="container-fluid">

    <h3 class="fw-bold mb-4">Daftar Pesanan</h3>

    <div class="card dashboard-card mb-4">
        <div class="card-body">
            <form action="{{ route('cashier.orders.list') }}" method="GET">
                <div class="row g-3 align-items-end">

                    <div class="col-md-5">
                        <label class="form-label">Cari Nomor Pesanan</label>
                        <input
                            type="text"
                            name="cari"
                            class="form-control"
                            placeholder="Masukkan nomor pesanan"
                            value="{{ request('cari') }}"
                        >
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Filter Status</label>
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="Baru" {{ request('status') == 'Baru' ? 'selected' : '' }}>Baru</option>
                            <option value="Diproses" {{ request('status') == 'Diproses' ? 'selected' : '' }}>Diproses</option>
                            <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="Dibatalkan" {{ request('status') == 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search"></i> Cari
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <div class="card dashboard-card">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No. Pesanan</th>
                            <th>Pelanggan</th>
                            <th>Meja</th>
                            <th>Waktu</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($pesanan as $item)
                            <tr>
                                <td>{{ $item->kode_pesanan }}</td>
                                <td>{{ $item->customer->nama ?? 'Pelanggan' }}</td>
                                <td>{{ $item->cafeTable->nomor_meja ?? '-' }}</td>
                                <td>{{ $item->tanggal_pesanan ? $item->tanggal_pesanan->format('d/m/Y H:i') : '-' }}</td>
                                <td>Rp{{ number_format($item->total, 0, ',', '.') }}</td>
                                <td>
                                    @if ($item->status_pesanan == 'Baru')
                                        <span class="badge bg-primary">Baru</span>
                                    @elseif ($item->status_pesanan == 'Diproses')
                                        <span class="badge bg-warning text-dark">Diproses</span>
                                    @elseif ($item->status_pesanan == 'Selesai')
                                        <span class="badge bg-success">Selesai</span>
                                    @else
                                        <span class="badge bg-danger">{{ $item->status_pesanan }}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('cashier.orders.show', $item->id) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        Buka
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    Tidak ada pesanan ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

        </div>
    </div>

</div>

@endsection
