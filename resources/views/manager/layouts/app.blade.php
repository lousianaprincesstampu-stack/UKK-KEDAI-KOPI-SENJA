<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Manager - Kedai Kopi Senja')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
  
        body {
            background-color: #f5f5f5;
            font-family: Arial, sans-serif;
            margin: 0;
        }

        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: #6f4e6f;
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            padding: 20px 15px;

             overflow-y: auto; 
            overflow-x: hidden; 
        }

        .sidebar {
            overscroll-behavior-y: contain;
            scrollbar-width: thin; /* Scrollbar tipis */
            scrollbar-color: #d6c3d6 #6f4e6f;
        }

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

        .sidebar a:hover,
        .sidebar a.active {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .main-content {
            margin-left: 250px;
            padding: 30px;
        }

        .dashboard-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        .card-icon {
            font-size: 28px;
            color: #6f4e6f;
        }

        .table-container {
            background-color: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }
        @media (max-width: 768px) {
            .sidebar {
                width: 200px;
            }

            .main-content {
                margin-left: 200px;
                padding: 15px;
            }
        }

        .sidebar .submenu {
            margin-left: 20px;
            margin-bottom: 10px;
        }

        .sidebar .submenu a {
            font-size: 14px;
            padding: 9px 12px;
        }
    </style>
</head>

<body>
    <div class="sidebar">

        <div class="brand">
            <div>
                <h5>Kedai Kopi Senja</h5>
                <small>Manager Panel</small>
            </div>
        </div>

        <a href="{{ route('manager.dashboard') }}"
           class="{{ request()->routeIs('manager.dashboard') ? 'active' : '' }}">
                Dashboard
        </a>

        <a href="{{ route('manager.menus.index') }}"
           class="{{ request()->routeIs('manager.menus.*') ? 'active' : '' }}">
                Data Menu
        </a>

        <a href="{{ route('manager.bahans.index') }}"
            class="sidebar-link {{ request()->routeIs('manager.bahans.*') ? 'active' : '' }}">
                Data Bahan
        </a>

        <a href="{{ route ('manager.recipes.index') }}"
            class="sidebar-link {{ request()->routeIs('manager.recipes.*') ? 'active' : '' }}">
                Resep Menu
        </a>

       <a href="{{ route('manager.stock-entries.index') }}" class="sidebar-link {{ request()->routeIs('manager.stock-entries.*') ? 'active' : '' }}">
                Stok Masuk
        </a>

        <a href="{{ route('manager.import-csv.index') }}"
            class="nav-link {{ request()->routeIs('manager.import-csv.*') ? 'active' : '' }}">
                <span>Import CSV</span>
        </a>

        <a href="#submenuLaporan"
            data-bs-toggle="collapse"
            class="{{ request()->routeIs('manager.reports.*') ? 'active' : '' }}">
                Laporan
            <i class="bi bi-chevron-down ms-auto"></i>
        </a>

        <div class="collapse submenu {{ request()->routeIs('manager.reports.*') ? 'show' : '' }}"
            id="submenuLaporan">

            <a href="{{ route('manager.reports.sales') }}">
                Penjualan
            </a>

            <a href="{{ route('manager.reports.low-stock') }}">
                Stok Menipis
            </a>

            <a href="{{ route('manager.reports.margin') }}">
                Margin Menu
            </a>

        </div>

        <form action="{{ route('logout') }}" method="POST" class="mt-4">
            <a href="{{ route('password.edit') }}"
                class="{{ request ()->routeIs('password.*') ? 'active' : '' }}">
                    Ganti Password
            </a>
            @csrf

            <button type="submit" class="btn btn-light w-100">
                <i class="bi bi-box-arrow-right"></i> Logout
            </button>
        </form>

    </div>

    <div class="main-content">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
