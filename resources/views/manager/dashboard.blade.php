
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Manager - Sistem Kafe</title>

    <!-- Menghubungkan Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Menghubungkan Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        /* Tampilan dasar halaman */
        body {
            background-color: #f5f5f5;
            font-family: Arial, sans-serif;
            margin: 0;
        }

        /* Sidebar di sebelah kiri */
        .sidebar {
            width: 250px;
            min-height: 100vh;
            background-color: #6f4e6f;
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            padding: 20px 15px;
        }

        /* Bagian logo dan nama kafe */
        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px;
            margin-bottom: 30px;
        }

        .brand-icon {
            font-size: 30px;
        }

        .brand h5 {
            margin: 0;
            font-weight: bold;
        }

        /* Link menu sidebar */
        .sidebar a {
            display: flex;
            align-items: center;
            gap: 12px;
            color: white;
            text-decoration: none;
            padding: 12px 15px;
            margin-bottom: 6px;
            border-radius: 8px;
        }

        /* Efek ketika menu diarahkan mouse */
        .sidebar a:hover,
        .sidebar a.active {
            background-color: rgba(255, 255, 255, 0.2);
        }

        /* Area konten utama */
        .main-content {
            margin-left: 250px;
            padding: 30px;
        }

        /* Kartu ringkasan */
        .dashboard-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        .card-icon {
            font-size: 28px;
            color: #6f4e6f;
        }

        /* Kotak tabel */
        .table-container {
            background-color: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        /* Tampilan sidebar pada layar kecil */
        @media (max-width: 768px) {
            .sidebar {
                width: 200px;
            }

            .main-content {
                margin-left: 200px;
                padding: 15px;
            }
        }
    </style>
</head>

<body>

    <!-- SIDEBAR -->
    <div class="sidebar">

        <!-- Logo dan nama kafe -->
        <div class="brand">
            <i class="bi bi-cup-hot-fill brand-icon"></i>
            <div>
                <h5>KAFE</h5>
                <small>Manager Panel</small>
            </div>
        </div>

        <!-- Daftar menu navigasi -->
        <a href="{{ route('manager.dashboard') }}" class="active">
            <i class="bi bi-grid"></i> Dashboard
        </a>

        <a href="#">
            <i class="bi bi-journal-text"></i> Data Menu
        </a>

        <a href="#">
            <i class="bi bi-box-seam"></i> Data Bahan
        </a>

        <a href="#">
            <i class="bi bi-book"></i> Resep Menu
        </a>

        <a href="#">
            <i class="bi bi-box-arrow-in-down"></i> Stok Masuk
        </a>

        <a href="#">
            <i class="bi bi-file-earmark-spreadsheet"></i> Import CSV
        </a>

        <a href="#">
            <i class="bi bi-clipboard-data"></i> Laporan
        </a>

        <!-- Tombol logout -->
        <form action="{{ route('logout') }}" method="POST" class="mt-4">
            @csrf
            <button type="submit" class="btn btn-light w-100">
                <i class="bi bi-box-arrow-right"></i> Logout
            </button>
        </form>

    </div>

    <!-- KONTEN UTAMA -->
    <div class="main-content">

        <!-- Judul halaman -->
        <div class="mb-4">
            <h3 class="fw-bold">Dashboard Manager</h3>
            <p class="text-muted">
                Selamat datang, {{ Auth::user()->name }}!
            </p>
        </div>

        <!-- KARTU RINGKASAN -->
        <div class="row g-3 mb-4">

            <!-- Total menu -->
            <div class="col-md-4">
                <div class="card dashboard-card">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total Menu</p>
                            <h3 class="fw-bold mb-0">0</h3>
                        </div>
                        <i class="bi bi-journal-text card-icon"></i>
                    </div>
                </div>
            </div>

            <!-- Total bahan -->
            <div class="col-md-4">
                <div class="card dashboard-card">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total Bahan</p>
                            <h3 class="fw-bold mb-0">0</h3>
                        </div>
                        <i class="bi bi-box-seam card-icon"></i>
                    </div>
                </div>
            </div>

            <!-- Penjualan hari ini -->
            <div class="col-md-4">
                <div class="card dashboard-card">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Penjualan Hari Ini</p>
                            <h3 class="fw-bold mb-0">Rp 0</h3>
                        </div>
                        <i class="bi bi-cash-stack card-icon"></i>
                    </div>
                </div>
            </div>

        </div>

        <!-- TABEL STOK BAHAN MENIPIS -->
        <div class="table-container">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">Stok Bahan Menipis</h5>
                <span class="badge text-bg-warning">Perlu Diperhatikan</span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">

                    <!-- Judul kolom tabel -->
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Bahan</th>
                            <th>Stok Saat Ini</th>
                            <th>Stok Minimum</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <!-- Contoh isi tabel sementara -->
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>Gula</td>
                            <td>2 Kg</td>
                            <td>5 Kg</td>
                            <td>
                                <span class="badge text-bg-danger">Menipis</span>
                            </td>
                        </tr>

                        <tr>
                            <td>2</td>
                            <td>Susu</td>
                            <td>3 Liter</td>
                            <td>5 Liter</td>
                            <td>
                                <span class="badge text-bg-danger">Menipis</span>
                            </td>
                        </tr>

                        <tr>
                            <td>3</td>
                            <td>Kopi</td>
                            <td>1 Kg</td>
                            <td>2 Kg</td>
                            <td>
                                <span class="badge text-bg-danger">Menipis</span>
                            </td>
                        </tr>
                    </tbody>

                </table>
            </div>

        </div>

    </div>

    <!-- Script Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
