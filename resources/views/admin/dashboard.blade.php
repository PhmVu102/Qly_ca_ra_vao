{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <div class="dash-wrap">

        {{-- ===== TOPBAR ===== --}}
        <div class="dash-topbar">
            <div>
                <h1>Dashboard</h1>
                <p>{{ now()->isoFormat('dddd, D MMMM YYYY') }}</p>
            </div>

            <div class="topbar-right">
                <div class="live-badge">
                    <span class="live-dot"></span> Live
                </div>

                <div class="admin-chip">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <div style="display: flex;align-items: center; cursor: pointer;">
                                <span>{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>{{ auth()->user()->name }}
                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                                 onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>
        </div>

        {{-- ===== STAT CARDS ===== --}}
        <div class="stat-grid">
            <div class="stat-card stat-total">
                <div class="stat-icon"><i class="ti ti-users"></i></div>
                <div class="stat-body">
                    <span class="stat-label">Tổng nhân viên</span>
                    <span class="stat-value">{{ $totalStaff }}</span>
                </div>
            </div>

            <div class="stat-card stat-present">
                <div class="stat-icon"><i class="ti ti-circle-check"></i></div>
                <div class="stat-body">
                    <span class="stat-label">Đi làm hôm nay</span>
                    <span class="stat-value">{{ $presentToday }}</span>
                    <span class="stat-sub">/ {{ $scheduledToday }} ca</span>
                </div>
            </div>

            <div class="stat-card stat-late">
                <div class="stat-icon"><i class="ti ti-clock-exclamation"></i></div>
                <div class="stat-body">
                    <span class="stat-label">Đi muộn</span>
                    <span class="stat-value">{{ $lateToday }}</span>
                </div>
            </div>

            <div class="stat-card stat-absent">
                <div class="stat-icon"><i class="ti ti-user-off"></i></div>
                <div class="stat-body">
                    <span class="stat-label">Vắng mặt</span>
                    <span class="stat-value">{{ $absentToday }}</span>
                </div>
            </div>

            <div class="stat-card stat-incomplete">
                <div class="stat-icon"><i class="ti ti-alert-triangle"></i></div>
                <div class="stat-body">
                    <span class="stat-label">Chưa đủ thông tin</span>
                    <span class="stat-value">{{ $incompleteToday }}</span>
                </div>
            </div>
        </div>

        {{-- ===== BỐ CỤC CHÍNH: KHỐI TRÁI (BIỂU ĐỒ) & KHỐI PHẢI (SỐ LIỆU CA, LOG, GPS) ===== --}}
        <div class="dash-row main-split-row">

            {{-- KHỐI TRÁI: Hệ thống biểu đồ 7 ngày và trạng thái --}}
            <div class="side-charts-column">
                {{-- Biểu đồ 7 ngày --}}
                <div class="card card-chart shadow-sm">
                    <div class="card-head">
                        <h2>Chấm công 7 ngày qua</h2>
                    </div>
                    <div class="chart-canvas-container">
                        <canvas id="weekChart" height="205"></canvas>
                    </div>
                </div>

                {{-- Tỉ lệ trạng thái hôm nay --}}
                <div class="card card-donut shadow-sm">
                    <div class="card-head">
                        <h2>Tỷ lệ chấm công hôm nay</h2>
                    </div>
                    <div class="donut-wrap">
                        <canvas id="statusChart" width="120" height="120"></canvas>
                        <div class="donut-legend">
                            <div class="legend-item"><span class="dot dot-present"></span> Đúng giờ <b>{{ $onTimeToday  }}</b></div>
                            <div class="legend-item"><span class="dot dot-late"></span> Muộn <b>{{ $lateToday }}</b></div>
                            <div class="legend-item"><span class="dot dot-early"></span> Về sớm <b>{{ $earlyLeaveToday }}</b></div>
                            <div class="legend-item"><span class="dot dot-absent"></span> Vắng <b>{{ $absentToday }}</b></div>
                            <div class="legend-item"><span class="dot dot-incomplete"></span> Thiếu log <b>{{ $incompleteToday }}</b></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="right-data-column">

                {{-- 1. Phân Hệ Tổng Quan Ca Đã Phân & Các Ca Làm Việc --}}
                <div class="card shadow-sm grid-two-columns">
                    <div class="sub-data-block border-r">
                        <div class="card-head-compact">
                            <h3><i class="ti ti-calendar-stats text-indigo"></i> Phân ca hôm nay</h3>

                            <a href="{{ route('admin.schedules.index') }}" class="text-xs text-blue-500">Chi tiết</a>
                        </div>

                        <div class="compact-list mt-3">
                            <div class="flex-justify-between py-1 border-b text-xs">
                                <span class="text-gray-500">Tổng ca điều phối:</span>

                                <span class="font-bold text-gray-800">{{ $scheduledToday ?? 0 }} ca</span>
                            </div>

                            <div class="flex-justify-between py-1 border-b text-xs">
                                <span class="text-gray-500">Nhân sự thực tế:</span>

                                <span class="font-bold text-emerald-600">{{ $presentToday ?? 0 }} đang làm</span>
                            </div>

                            <div class="flex-justify-between py-1 text-xs">
                                <span class="text-gray-500">Ca chưa check-in:</span>

                                <span class="font-bold text-rose-500">{{ max(0, ($scheduledToday ?? 0) - ($presentToday ?? 0)) }} ca</span>
                            </div>
                        </div>
                    </div>

                    {{-- Sub-card: Danh sách cấu hình các ca làm việc thực tế --}}
                    <div class="sub-data-block">
                        <div class="card-head-compact">
                            <h3><i class="ti ti-alarm text-purple"></i> Khung giờ ca làm việc</h3>

                            <a href="{{ route('admin.shifts.index') }}" class="text-xs text-blue-500">Cấu hình</a>
                        </div>

                        <div class="compact-list shift-mini-scroll mt-2">
                            @forelse($activeShifts as $shift)
                                <div class="shift-mini-item">
                                    <span class="shift-mini-name">{{ $shift->name }}</span>

                                    <span class="shift-mini-time">{{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}</span>
                                </div>
                            @empty
                                <div class="text-center py-4 text-[11px] text-gray-400 italic">
                                    <i class="ti ti-info-circle mb-1 block text-sm"></i> Chưa có ca làm việc nào
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- 2. Phân Hệ Nhật Ký Dữ Liệu Chấm Công Mới Nhất --}}
                <div class="card shadow-sm">
                    <div class="card-head-compact">
                        <h3><i class="ti ti-device-analytics text-emerald"></i> Log chấm công thời gian thực</h3>

                        <a href="{{ route('admin.attendance.index') }}" class="text-xs text-blue-500">Xem tất cả</a>
                    </div>

                    <div class="realtime-log-container mt-2">
                        @forelse($recentLogs as $log)
                            <div class="realtime-log-row">
                                <div class="log-left-part">
                                    <span class="avatar-sm">{{ strtoupper(substr($log->user->name ?? '?', 0, 1)) }}</span>

                                    <div>
                                        <p class="user-log-name">{{ $log->user->name ?? 'Nhân sự đã xóa' }}</p>

                                        <span class="user-log-sub">{{ $log->workSchedule->shift->name ?? 'Ca tự do' }}</span>
                                    </div>
                                </div>

                                <div class="log-right-part">
                                    @if($log->log_type === 'check_out')
                                        <span class="badge-status bg-rose-100 text-rose-700">Check-Out</span>
                                    @else
                                        <span class="badge-status bg-emerald-100 text-emerald-700">Check-In</span>
                                    @endif
                                    <span class="log-timestamp">{{ \Carbon\Carbon::parse($log->scan_time)->format('H:i:s') }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5 text-xs text-gray-400">

                                <i class="ti ti-database-off text-lg block mb-1"></i> Hôm nay chưa có dữ liệu quét công.
                            </div>
                        @endforelse
                    </div>
                </div>
        <div class="dash-row full-width-row">
            <div class="card card-late shadow-sm">
                <div class="card-head">
                    <h2>Đi muộn hôm nay</h2>
                    <a href="{{ route('admin.attendance.index') }}" class="see-all">Xem tất cả <i class="ti ti-arrow-right"></i></a>
                </div>
                <div class="log-list">
                    @forelse($lateStaff as $att)
                        <div class="log-row">
                            <div class="log-avatar">{{ strtoupper(substr($att->user->name, 0, 1)) }}</div>
                            <div class="log-info">
                                <strong>{{ $att->user->name }}</strong>
                                <span>{{ $att->user->department->name ?? '—' }}</span>
                            </div>
                            <div class="log-meta">
                                <span class="log-time">{{ \Carbon\Carbon::parse($att->check_in_time)->format('H:i') }}</span>
                                <span class="badge-late">+{{ $att->late_minutes }}p</span>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">
                            <i class="ti ti-mood-happy"></i>
                            <p>Không ai đi muộn hôm nay!</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
            </div>
        </div>

    </div>
@endsection

@push('styles')
    <style>
        :root {
            --c-present:    #16a34a;
            --c-late:       #d97706;
            --c-early:      #7c3aed;
            --c-absent:     #dc2626;
            --c-incomplete: #6b7280;
            --c-total:      #0369a1;
            --surface:      #ffffff;
            --surface-2:    #f8fafc;
            --border:       #e2e8f0;
            --text-1:       #0f172a;
            --text-2:       #64748b;
            --radius:       14px;
        }

        .dash-wrap {
            padding: 1.5rem 2rem;
            max-width: 1280px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            font-family: 'DM Sans', sans-serif;
        }

        /* ---- Topbar ---- */
        .dash-topbar { display: flex; align-items: center; justify-content: space-between; }
        .dash-topbar h1 { font-size: 1.5rem; font-weight: 700; color: var(--text-1); margin: 0 0 2px; }
        .dash-topbar p  { font-size: 0.85rem; color: var(--text-2); margin: 0; text-transform: capitalize; }
        .topbar-right   { display: flex; align-items: center; gap: 0.75rem; }
        .live-badge {
            display: flex; align-items: center; gap: 6px;
            padding: 4px 12px; border-radius: 99px;
            background: #dcfce7; color: var(--c-present);
            font-size: 0.78rem; font-weight: 600;
        }
        .live-dot {
            width: 7px; height: 7px; border-radius: 50%;
            background: var(--c-present);
            animation: pulse-dot 1.5s ease-in-out infinite;
        }
        @keyframes pulse-dot {
            0%,100% { opacity: 1; transform: scale(1); }
            50%      { opacity: .5; transform: scale(1.3); }
        }
        .admin-chip {
            display: flex; align-items: center; gap: 8px;
            padding: 6px 14px; border-radius: 99px;
            background: var(--surface); border: 1px solid var(--border);
            font-size: 0.82rem; color: var(--text-1); font-weight: 500;
        }
        .admin-chip span {
            width: 26px; height: 26px; border-radius: 50%;
            background: #0369a1; color: white;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.75rem; font-weight: 700; margin-right: 6px;
        }

        /* ---- Stat Grid ---- */
        .stat-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 1rem; }
        @media(max-width: 1024px) { .stat-grid { grid-template-columns: repeat(3, 1fr); } }
        @media(max-width: 640px)  { .stat-grid { grid-template-columns: repeat(2, 1fr); } }

        .stat-card {
            background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius);
            padding: 1.25rem; display: flex; align-items: center; gap: 1rem;
            transition: transform 0.15s, box-shadow 0.15s;
        }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 16px rgba(0,0,0,.06); }
        .stat-icon {
            width: 44px; height: 44px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center; font-size: 1.3rem; flex-shrink: 0;
        }
        .stat-body { display: flex; flex-direction: column; gap: 2px; }
        .stat-label { font-size: 0.75rem; color: var(--text-2); font-weight: 500; }
        .stat-value { font-size: 1.75rem; font-weight: 700; line-height: 1; }
        .stat-sub   { font-size: 0.75rem; color: var(--text-2); }

        .stat-total      .stat-icon { background: #e0f2fe; color: var(--c-total); }
        .stat-total      .stat-value { color: var(--c-total); }
        .stat-present    .stat-icon { background: #dcfce7; color: var(--c-present); }
        .stat-present    .stat-value { color: var(--c-present); }
        .stat-late       .stat-icon { background: #fef3c7; color: var(--c-late); }
        .stat-late       .stat-value { color: var(--c-late); }
        .stat-absent     .stat-icon { background: #fee2e2; color: var(--c-absent); }
        .stat-absent     .stat-value { color: var(--c-absent); }
        .stat-incomplete .stat-icon { background: #f1f5f9; color: var(--c-incomplete); }
        .stat-incomplete .stat-value { color: var(--c-incomplete); }

        /* ---- GRID BỐ CỤC: TRÁI (4.5) - PHẢI (7.5) ---- */
        .dash-row { display: grid; gap: 1.25rem; }
        .main-split-row { grid-template-columns: 4.5fr 7.5fr; align-items: start; }
        .full-width-row { grid-template-columns: 1fr; }
        @media(max-width: 1024px) { .main-split-row { grid-template-columns: 1fr; } }

        /* Cột dọc chứa cụm biểu đồ bên trái */
        .side-charts-column { display: flex; flex-direction: column; gap: 1.25rem; }
        .card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 1.25rem; }
        .card-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; }
        .card-head h2 { font-size: 0.9rem; font-weight: 600; color: var(--text-1); margin: 0; }
        .see-all { font-size: 0.78rem; color: #3b82f6; text-decoration: none; display: flex; align-items: center; gap: 3px; }
        .see-all:hover { text-decoration: underline; }

        .chart-canvas-container { width: 100%; height: auto; position: relative; }

        /* ---- Donut chart ---- */
        .donut-wrap { display: flex; align-items: center; gap: 1rem; justify-content: flex-start; padding: 0.25rem 0; }
        .donut-legend { display: grid; grid-template-columns: repeat(1, 1fr); gap: 0.35rem; flex: 1; }
        .legend-item  { display: flex; align-items: center; gap: 6px; font-size: 0.75rem; color: var(--text-2); }
        .legend-item b { color: var(--text-1); margin-left: auto; font-variant-numeric: tabular-nums; }
        .dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
        .dot-present    { background: var(--c-present); }
        .dot-late       { background: var(--c-late); }
        .dot-early      { background: var(--c-early); }
        .dot-absent     { background: var(--c-absent); }
        .dot-incomplete { background: var(--c-incomplete); }

        /* ==================== CÁC PHÂN HỆ MỚI KHỐI PHẢI ==================== */
        .right-data-column { display: flex; flex-direction: column; gap: 1.25rem; }

        .card-head-compact { display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #f1f5f9; padding-bottom: 8px; }
        .card-head-compact h3 { font-size: 0.85rem; font-weight: 600; color: var(--text-1); margin: 0; display: flex; align-items: center; gap: 6px; }
        .card-head-compact i { font-size: 1.1rem; }

        .grid-two-columns { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; }
        @media(max-width: 640px) { .grid-two-columns { grid-template-columns: 1fr; } .border-r { border-right: none !important; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px; } }

        .border-r { border-right: 1px solid #e2e8f0; padding-right: 1.25rem; }
        .flex-justify-between { display: flex; justify-content: space-between; align-items: center; }

        /* Mini shift render */
        .shift-mini-scroll { max-height: 95px; overflow-y: auto; display: flex; flex-direction: column; gap: 4px; }
        .shift-mini-item { display: flex; justify-content: space-between; background: #f8fafc; padding: 4px 8px; border-radius: 6px; font-size: 11px; }
        .shift-mini-name { color: var(--text-1); font-weight: 500; }
        .shift-mini-time { color: #4f46e5; font-weight: 600; font-variant-numeric: tabular-nums; }

        /* Realtime log rows */
        .realtime-log-container { display: flex; flex-direction: column; gap: 6px; max-height: 180px; overflow-y: auto; }
        .realtime-log-row { display: flex; justify-content: space-between; align-items: center; padding: 6px 10px; background: #f8fafc; border-radius: 8px; transition: background .15s; }
        .realtime-log-row:hover { background: #f1f5f9; }
        .log-left-part { display: flex; align-items: center; gap: 8px; }
        .avatar-sm { width: 28px; height: 28px; border-radius: 50%; background: #e0f2fe; color: #0369a1; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; }
        .user-log-name { font-size: 12px; font-weight: 600; margin: 0; color: var(--text-1); }
        .user-log-sub { font-size: 10px; color: var(--text-2); display: block; }
        .log-right-part { display: flex; align-items: center; gap: 10px; }
        .badge-status { font-size: 10px; font-weight: 600; padding: 2px 6px; border-radius: 4px; }
        .log-timestamp { font-size: 11px; font-weight: 500; color: #475569; font-variant-numeric: tabular-nums; }

        /* GPS rows */
        .gps-list { display: flex; flex-direction: column; gap: 6px; }
        .gps-item-row { display: flex; justify-content: space-between; align-items: center; padding: 6px 10px; border: 1px solid #f1f5f9; border-radius: 8px; }
        .gps-info { display: flex; align-items: center; gap: 8px; }
        .gps-indicator { width: 6px; height: 6px; border-radius: 50%; background: #3b82f6; display: inline-block; position: relative; }
        .gps-indicator.present { background: var(--c-present); }
        .gps-indicator.absent { background: var(--c-absent); }
        .gps-user { font-size: 12px; font-weight: 600; margin: 0; color: var(--text-1); }
        .gps-coords { font-size: 11px; color: var(--text-2); font-variant-numeric: tabular-nums; }
        .gps-meta { text-align: right; display: flex; flex-direction: column; gap: 2px; }
        .gps-time { font-size: 10px; color: #94a3b8; font-variant-numeric: tabular-nums; }
        .gps-type-badge { font-size: 10px; font-weight: 600; color: #475569; }
        .gps-type-badge.in { color: var(--c-present); }
        .gps-type-badge.out { color: var(--c-early); }

        /* Colors utility */
        .text-indigo { color: #4f46e5; }
        .text-purple { color: #7c3aed; }
        .text-emerald { color: #059669; }
        .text-rose { color: #e11d48; }

        /* ---- Log list ---- */
        .log-list { display: flex; flex-direction: column; gap: 0.5rem; }
        .log-row { display: flex; align-items: center; gap: 0.75rem; padding: 0.65rem 0.75rem; border-radius: 8px; transition: background 0.15s; }
        .log-row:hover { background: var(--surface-2); }
        .log-avatar {
            width: 34px; height: 34px; border-radius: 50%; background: #dbeafe; color: #1d4ed8;
            display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700; flex-shrink: 0;
        }
        .log-info { flex: 1; }
        .log-info strong { display: block; font-size: 0.85rem; color: var(--text-1); font-weight: 600; }
        .log-info span   { font-size: 0.75rem; color: var(--text-2); }
        .log-meta { display: flex; flex-direction: column; align-items: flex-end; gap: 3px; }
        .log-time { font-size: 0.78rem; color: var(--text-2); font-variant-numeric: tabular-nums; }
        .badge-late { font-size: 0.72rem; font-weight: 700; background: #fef3c7; color: var(--c-late); padding: 2px 8px; border-radius: 99px; }
        .empty-state { text-align: center; padding: 2rem 1rem; color: var(--text-2); font-size: 0.85rem; }
        .empty-state i { font-size: 1.8rem; display: block; margin-bottom: 0.4rem; }
        .empty-state p { margin: 0; }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
    <script>
        const weekLabels  = @json($weekLabels);
        const weekPresent = @json($weekPresent);
        const weekLate    = @json($weekLate);
        const weekAbsent  = @json($weekAbsent);

        // ---- Biểu đồ 7 ngày ----
        new Chart(document.getElementById('weekChart'), {
            type: 'bar',
            data: {
                labels: weekLabels,
                datasets: [
                    { label: 'Đúng giờ', data: weekPresent, backgroundColor: '#16a34a', borderRadius: 4, borderSkipped: false },
                    { label: 'Muộn',     data: weekLate,    backgroundColor: '#d97706', borderRadius: 4, borderSkipped: false },
                    { label: 'Vắng',     data: weekAbsent,  backgroundColor: '#dc2626', borderRadius: 4, borderSkipped: false },
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { legend: { position: 'top', labels: { boxWidth: 8, font: { size: 10 } } } },
                scales: {
                    x: { stacked: true, grid: { display: false }, ticks: { font: { size: 10 } } },
                    y: { stacked: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 }, stepSize: 1 } }
                }
            }
        });

        // ---- Donut chart ----
        new Chart(document.getElementById('statusChart'), {
            type: 'doughnut',
            data: {
                labels: ['Đúng giờ', 'Muộn', 'Về sớm', 'Vắng', 'Thiếu log'],
                datasets: [{
                    data: [
                        {{ $onTimeToday  }},
                        {{ $lateToday }},
                        {{ $earlyLeaveToday }},
                        {{ $absentToday }},
                        {{ $incompleteToday }}
                    ],
                    backgroundColor: ['#16a34a','#d97706','#7c3aed','#dc2626','#6b7280'],
                    borderWidth: 2,
                    borderColor: '#fff',
                    hoverOffset: 4,
                }]
            },
            options: {
                cutout: '75%',
                plugins: { legend: { display: false } },
                responsive: false,
            }
        });
    </script>
@endpush
