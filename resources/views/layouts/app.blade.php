<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') · {{ config('app.name') }}</title>
    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.min.css') }}" rel="stylesheet">
    <style>
        body { background: #f4f6f9; }
        .sidebar { width: 250px; min-height: 100vh; background: #1e293b; }
        .sidebar .nav-link { color: #cbd5e1; border-radius: .5rem; margin-bottom: 2px; }
        .sidebar .nav-link:hover { background: #334155; color: #fff; }
        .sidebar .nav-link.active { background: #2563eb; color: #fff; }
        .sidebar .menu-label { color: #64748b; font-size: .72rem; letter-spacing: .08em; text-transform: uppercase; }
        .card { border: 0; box-shadow: 0 1px 3px rgba(0,0,0,.08); }
        .table th { white-space: nowrap; }
        @media (max-width: 991.98px) { .sidebar { width: 100%; min-height: auto; } }
    </style>
    @stack('styles')
</head>
<body>
<div class="d-lg-flex">
    <nav class="sidebar p-3 d-flex flex-column">
        <a href="{{ route('dashboard') }}" class="d-flex align-items-center text-white text-decoration-none mb-3">
            <i class="bi bi-diagram-3-fill fs-4 me-2 text-primary"></i>
            <div>
                <div class="fw-bold">SPK SMART</div>
                <small class="text-secondary">Sistem Pendukung Keputusan</small>
            </div>
        </a>
        @php $isAdmin = auth()->user()->role === 'admin'; @endphp
        <ul class="nav flex-column">
            <li><a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
            @if ($isAdmin)
                <li class="menu-label mt-3 mb-1 px-2">Data Master</li>
                <li><a class="nav-link {{ request()->routeIs('kriteria.*') ? 'active' : '' }}" href="{{ route('kriteria.index') }}"><i class="bi bi-list-check me-2"></i>Kriteria & Sub Kriteria</a></li>
                <li><a class="nav-link {{ request()->routeIs('alternatif.*') ? 'active' : '' }}" href="{{ route('alternatif.index') }}"><i class="bi bi-people me-2"></i>Alternatif</a></li>
                <li><a class="nav-link {{ request()->routeIs('penilaian.*') ? 'active' : '' }}" href="{{ route('penilaian.index') }}"><i class="bi bi-pencil-square me-2"></i>Penilaian</a></li>
            @endif
            <li class="menu-label mt-3 mb-1 px-2">Metode SMART</li>
            <li><a class="nav-link {{ request()->routeIs('perhitungan.*') ? 'active' : '' }}" href="{{ route('perhitungan.index') }}"><i class="bi bi-calculator me-2"></i>Perhitungan</a></li>
            <li><a class="nav-link {{ request()->routeIs('hasil.*') ? 'active' : '' }}" href="{{ route('hasil.index') }}"><i class="bi bi-trophy me-2"></i>Hasil & Ranking</a></li>
            @if ($isAdmin)
                <li class="menu-label mt-3 mb-1 px-2">Pengaturan</li>
                <li><a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}"><i class="bi bi-person-gear me-2"></i>Pengguna</a></li>
            @endif
        </ul>
    </nav>

    <main class="flex-grow-1">
        <header class="bg-white border-bottom px-4 py-3 d-flex justify-content-between align-items-center">
            <h1 class="h5 mb-0">@yield('title', 'Dashboard')</h1>
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle me-1"></i>{{ auth()->user()->name }}
                    <span class="badge text-bg-secondary ms-1">{{ ucfirst(auth()->user()->role) }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <form method="POST" action="{{ route('logout') }}">@csrf
                            <button class="dropdown-item"><i class="bi bi-box-arrow-right me-2"></i>Keluar</button>
                        </form>
                    </li>
                </ul>
            </div>
        </header>
        <div class="p-4">
            @include('partials.alert')
            @yield('content')
        </div>
    </main>
</div>
<script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script>
    document.querySelectorAll('form[data-confirm]').forEach(f => f.addEventListener('submit', e => {
        if (!confirm(f.dataset.confirm)) e.preventDefault();
    }));
</script>
@stack('scripts')
</body>
</html>
