@extends('cashier.layouts.app')
@section('title', 'Detail Pesanan - Kedai Kopi Senja')
@section('content')
<div class="container-fluid">

    <div class="mb-4">
        <h3 class="fw-bold">Detail Pesanan</h3>
        <p class="text-muted">
            Informasi lengkap pesanan pelanggan.
        </p>
    </div>

    <div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="row g-3">

            <div class="col-md-4">
                <small class="text-muted">No. Pesanan</small>
                <h6 class="fw-bold">{{ $pesanan->kode_pesanan }}</h6>
            </div>

            <div class="col-md-4">
                <small class="text-muted">Pelanggan</small>
                <h6 class="fw-bold">{{ $pesanan->customer->nama ?? '-' }}</h6>
            </div>

            <div class="col-md-4">
                <small class="text-muted">Meja</small>
                <h6 class="fw-bold">{{ $pesanan->cafeTable->nomor_meja ?? '-' }}</h6>
            </div>

            <div class="col-md-4">
                <small class="text-muted">Waktu Pesanan</small>
                <h6 class="fw-bold">
                    {{ $pesanan->tanggal_pesanan?->format('d-m-Y H:i') ?? '-' }}
                </h6>
            </div>

            <div class="col-md-4">
                <small class="text-muted">Status Pesanan</small>
                <h6 class="fw-bold">{{ $pesanan->status_pesanan }}</h6>
            </div>

            <div class="col-md-4">
                <small class="text-muted">Metode Pembayaran</small>
                <h6 class="fw-bold">{{ $pesanan->metode_pembayaran ?? '-' }}</h6>
            </div>

        </div>
    </div>
</div>
    @if ($pesanan->status_pesanan == 'Baru' || $pesanan->status_pesanan == 'Diproses')
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h5 class="fw-bold mb-3">Ubah Status Pesanan</h5>

            <form action="{{ route('cashier.orders.update-status', $pesanan->id) }}" method="POST">
                @csrf
                @method('PATCH')

                @if ($pesanan->status_pesanan == 'Baru')
                    <input type="hidden" name="status_pesanan" value="Diproses">

                    <p class="text-muted">Pesanan ini belum diproses.</p>

                    <button type="submit" class="btn btn-primary">
                        Proses Pesanan
                    </button>
                @elseif ($pesanan->status_pesanan == 'Diproses')
                    <input type="hidden" name="status_pesanan" value="Selesai">

                    <p class="text-muted">Pesanan sedang diproses.</p>

                    <button type="submit" class="btn btn-success">
                        Tandai Selesai
                    </button>
                @endif
            </form>
        </div>
    </div>
@endif

    <div class="card border-0 shadow-sm">
        <div class="card-body">

            <h5 class="fw-bold mb-3">Daftar Menu Pesanan</h5>

            <div class="table-responsive">
                <table class="table table-hover align-middle">

                    <thead class="table-light">
                        <tr>
                            <th>Nama Menu</th>
                            <th>Jumlah</th>
                            <th>Harga</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($pesanan->orderDetails as $detail)
                            <tr>
                                <td>{{ $detail->nama_menu }}</td>
                                <td>{{ $detail->jumlah }}</td>
                                <td>
                                    Rp {{ number_format($detail->harga, 0, ',', '.') }}
                                </td>
                                <td>
                                    Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    Belum ada detail menu.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

            <div class="text-end mt-3">
                <p class="mb-1">
                    Subtotal: Rp {{ number_format($pesanan->subtotal, 0, ',', '.') }}
                </p>

                <p class="mb-1">
                    Service: Rp {{ number_format($pesanan->service, 0, ',', '.') }}
                </p>

                <p class="mb-1">
                    Pajak: Rp {{ number_format($pesanan->pajak, 0, ',', '.') }}
                </p>

                <h5 class="fw-bold mt-2">
                    Total: Rp {{ number_format($pesanan->total, 0, ',', '.') }}
                </h5>
            </div>

            <div class="mt-4">
                <a href="{{ route('cashier.orders.index') }}"
                   class="btn btn-secondary">
                    Kembali
                </a>
            </div>

        </div>
    </div>

</div>
@endsection
