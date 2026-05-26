<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin - Quản lý Chấm Công')</title>

    <link rel="icon" type="image/png" href="{{ asset('images/logo-hunonic-ngang.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo-hunonic-ngang.png') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --sidebar-width: 260px;
        }
        .admin-layout {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: var(--sidebar-width);
            background: #1e2937;
            color: #e2e8f0;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 100;
        }
        .main-content {
            margin-left: var(--sidebar-width);
            flex: 1;
            background: #f8fafc;
            min-height: 100vh;
        }
        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 20px;
            color: #cbd5e1;
            text-decoration: none;
            transition: all 0.2s;
        }
        .nav-link:hover, .nav-link.active {
            background: #334155;
            color: white;
        }
        .nav-link i {
            font-size: 1.2rem;
        }
    </style>
</head>
<body class="admin-layout">

    <!-- SIDEBAR -->
    <div class="sidebar">
        <div class="p-6 border-b border-slate-700">
            <h2 class="text-2xl font-bold text-white flex items-center gap-3">
                <span>Manager Word</span>
            </h2>
            <p class="text-slate-400 text-sm mt-1">Quản lý chấm công</p>
        </div>

        <nav class="mt-6">
            <a href="{{ route('admin.dashboard') }}"
            class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="ti ti-layout-dashboard"></i>
                <span>Dashboard</span>
            </a>

            <div class="px-6 mt-8 mb-2 text-xs font-semibold text-slate-400 uppercase tracking-widest">Quản lý nhân sự</div>

            <a href="{{ route('admin.staff.index') }}"
            class="nav-link {{ request()->routeIs('admin.staff.index') ? 'active' : '' }}">
                <i class="ti ti-users"></i>
                <span>Tài khoản</span>
            </a>

            <a href="{{ route('admin.departments.index') }}"
            class="nav-link {{ request()->routeIs('admin.departments.index') ? 'active' : '' }}">
                <i class="ti ti-building"></i>
                <span>Phòng ban</span>
            </a>

            <div class="px-6 mt-8 mb-2 text-xs font-semibold text-slate-400 uppercase tracking-widest">Quản lý ca làm</div>

            <a href="{{ route('admin.shifts.index') }}"
            class="nav-link {{ request()->routeIs('admin.shifts.index') ? 'active' : '' }}">
                <i class="ti ti-clock"></i>
                <span>Ca làm việc</span>
            </a>

            <a href="{{ route('admin.schedules.index') }}"
            class="nav-link {{ request()->routeIs('admin.schedules.index') ? 'active' : '' }}">
                <i class="ti ti-calendar"></i>
                <span>Phân ca</span>
            </a>

            <div class="px-6 mt-8 mb-2 text-xs font-semibold text-slate-400 uppercase tracking-widest">Chấm công & Vị trí</div>

            <a href="{{ route('admin.attendance.index') }}"
            class="nav-link {{ request()->routeIs('admin.attendance.index') ? 'active' : '' }}">
                <i class="ti ti-clipboard-check"></i>
                <span>Dữ liệu chấm công</span>
            </a>

            <a href="{{ route('admin.locations.index') }}"
            class="nav-link {{ request()->routeIs('admin.locations.index') ? 'active' : '' }}">
                <i class="ti ti-map-pin"></i>
                <span>Vị trí GPS</span>
            </a>
        </nav>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        @if (session('success'))
            <div class="mx-6 mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-3 text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mx-6 mt-6 rounded-xl border border-red-200 bg-red-50 px-5 py-3 text-red-700">
                {{ session('error') }}
            </div>
        @endif

        @if (isset($errors) && $errors->any())
            <div class="mx-6 mt-6 rounded-xl border border-red-200 bg-red-50 px-5 py-3 text-red-700">
                <div class="font-semibold">Du lieu chua hop le:</div>
                <ul class="mt-2 list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </div>

    @stack('styles')
    @stack('scripts')
</body>
</html>
