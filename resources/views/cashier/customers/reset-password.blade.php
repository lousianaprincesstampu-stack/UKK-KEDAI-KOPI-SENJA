@extends('cashier.layouts.app')
@section('title', 'Reset Password Pelanggan')
@section('content')

<div class="container-fluid">

    <h3 class="fw-bold mb-4">Reset Password Pelanggan</h3>

    <div class="card border-0 shadow-sm" style="max-width: 650px;">
        <div class="card-body p-4">

            <h5 class="fw-bold mb-2">Reset Password Pelanggan</h5>

            <p class="text-muted mb-4">
                Masukkan nomor HP pelanggan untuk membuat password sementara.
            </p>

            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('password_sementara'))
                <div class="alert alert-warning">
                    <strong>Password sementara berhasil dibuat!</strong>

                    <div class="mt-2">
                        Nama pelanggan:
                        <strong>{{ session('nama_customer') }}</strong>
                    </div>

                    <div class="mt-2">
                        Password sementara:
                        <strong class="fs-5">{{ session('password_sementara') }}</strong>
                    </div>

                    <p class="mb-0 mt-2 small">
                        Berikan password ini kepada pelanggan.
                        Pelanggan wajib mengganti password saat login berikutnya.
                    </p>
                </div>
            @endif

            <form action="{{ route('cashier.customers.reset-password.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="no_hp" class="form-label fw-semibold">
                        Nomor HP Pelanggan
                    </label>

                    <input
                        type="text"
                        name="no_hp"
                        id="no_hp"
                        class="form-control"
                        value="{{ old('no_hp') }}"
                        placeholder="Masukkan nomor HP pelanggan"
                        required
                    >

                    @error('no_hp')
                        <div class="text-danger small mt-1">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-key me-1"></i>
                    Reset Password
                </button>
            </form>

        </div>
    </div>

</div>

@endsection
