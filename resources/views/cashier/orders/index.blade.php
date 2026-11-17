@extends('cashier.layouts.app')
@section('title', 'Pesanan Masuk - Kedai Kopi Senja')
@section('content')

<div class="container-fluid">

    <div class="mb-4">
        <h3 class="fw-bold">Pesanan Masuk</h3>
        <p class="text-muted">
            Daftar pesanan pelanggan yang perlu ditangani.
        </p>
    </div>

    <div class="card border-0 shadow-sm">
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

                                <td>
                                    {{ $item->customer->nama ?? '-' }}
                                </td>

                                <td>
                                    {{ $item->cafeTable->nomor_meja ?? '-' }}
                                </td>

                                <td>
                                    {{ $item->tanggal_pesanan?->format('H:i') ?? '-' }}
                                </td>

                                <td>
                                    Rp {{ number_format($item->total, 0, ',', '.') }}
                                </td>

                                <td>
                                    @if ($item->status_pesanan == 'Baru')
                                        <span class="badge bg-warning text-dark">
                                            Baru
                                        </span>
                                    @elseif ($item->status_pesanan == 'Diproses')
                                        <span class="badge bg-primary">
                                            Diproses
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    <a href="{{ route('cashier.orders.show', $item->id) }}"
                                       class="btn btn-sm btn-primary">
                                        Buka
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    Belum ada pesanan masuk.
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
