<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Manager - Sistem Kafe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet".>
    <style>
        body{
            background-color: #f5f5f5;
        }

        .navbar {
            background-color: #6f4e6f;
        }

        .dashboard-card{
            border: none;
            border-radius: 10px;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-dark">
    <div class="container">
        <span class="navbar-brand fw-bold">Kafe - Manager</span>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-light btn-sm">logout</button>
        </form>
    </div>
    </nav>

    <div class="container py-4">
        <h3>Dashboard Manager</h3>
        <p class="text-muted">
            Selamat Datang, {{ Auth::user()->name }}.
        </p>

        <div class="row g-3 mt-2">
            <div class="col-md-4">
                <div class="card dashboard-card shadow-sm">
                    <div class="card-body">
                        <h5>Data bahan</h5>
                        <p class="text-muted mb-0">Kelola Bahan Dan Stok Kafe.</p>
        
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card dashboard-card shadow-sm">
                    <div class="card-body">
                        <h5>Data Menu</h5>
                        <p class="text-muted mb-0">Kelola Menu dan Harga Jual.</p>
                    </div>
                </div>
            </div>

            <div class="card dashboard-card shadow-sm">
                <div class="card-body">
                    <h5>Laporan</h5>
                    <p class="text-muted mb-0">Lihat Laporan Penjualan dan Stok.</p>
                </div>
            </div>
        </div>

        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>