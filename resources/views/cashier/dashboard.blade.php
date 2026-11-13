<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Kasir - Sistem Kafe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body{
            background-color: #f5f5f5;
        }
        .navbar{
            background-color: #6f4e6f;
        }
        .dashboard-card {
            border: name;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark">
        <div class="container">
            <span class="navbar-brand fw-bold">Kafe - Kasir</span>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-light btn-sm"></button>Logout</button>
            </form>
        </div>
    </nav>

    <div class="container py-4">
        <h3>Dashboard Kasir</h3>
        <p class="text-muted">
            Selamat Datang, {{ Auth:: user()-> name }}.
        </p>

        <div class="col-md-6">
            <div class="card dashboard-card drop-shadow-sm">
                <div class="card-body">
                    <h5>Transaksi Pesanan</h5>
                    <p class="text-muted mb-0"> Buat dan Kelola Pelanggan</p>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card dashboard-card shadow-sm">
                <div class="card-body">
                    <h5>Daftar Menu</h5>
                    <p class="text-muted mb-0">Lihat menu yang tersedia untuk dipesan</p>
                </div>
            </div>
        </div>

    </div>
</div>

 <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>