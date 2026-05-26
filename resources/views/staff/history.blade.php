@extends('layouts.app')

@section('title', 'Lịch sử chấm công')

@section('content')
    @php
        function statusBadge($status) {
            return match($status) {
                'present'          => ['Đúng giờ', 'ok'],
                'late'             => ['Đi muộn', 'warn'],
                'early_leave'      => ['Về sớm', 'info'],
                'late_early_leave' => ['Đi muộn + Về sớm', 'bad'],
                'incomplete'       => ['Muộn quá nửa ca', 'muted'],
                'forgot_checkout'  => ['Quên check-out', 'bad'],
                'working'          => ['Đang làm việc', 'working'],
                default            => [$status ?? 'Không rõ', 'muted'],
            };
        }
    @endphp

    <div class="history-shell">
        <!-- HEADER -->
        <section class="history-hero">
            <div>
                <h1>Lịch sử chấm công</h1>
                <span>Theo dõi toàn bộ lần chấm công của bạn</span>
            </div>

            <div class="hero-card">
                <span>Tổng bản ghi</span>
                <strong>{{ $attendances->total() }}</strong>
            </div>
        </section>

        <!-- TABLE -->
        <section class="panel history-panel">

            <div class="panel-head">
                <div>
                    <p class="eyebrow">Chi tiết</p>
                    <h2>Lịch sử làm việc</h2>
                </div>
            </div>

            <div class="table-wrap">
                <table class="history-table">
                    <thead>
                    <tr>
                        <th>Ngày</th>
                        <th>Ca làm</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                        <th>Tổng giờ</th>
                        <th>Đi muộn</th>
                        <th>Về sớm</th>
                        <th class="center">Trạng thái</th>
                    </tr>
                    </thead>

                    <tbody>
                    @forelse($attendances as $attendance)
                        @php
                            [$text, $class] = statusBadge($attendance->status);
                        @endphp
                        <tr>
                            <td>
                                <div class="date-cell">
                                    <strong>
                                        {{ \Carbon\Carbon::parse($attendance->work_date)->format('d') }}
                                    </strong>
                                    <span>
                                        {{ \Carbon\Carbon::parse($attendance->work_date)->format('m/Y') }}
                                    </span>
                                </div>
                            </td>

                            <td>
                                <span class="shift-name">
                                    {{ $attendance->shift_name ?? '—' }}
                                </span>
                            </td>

                            <td>
                                <div class="time-chip in">
                                    <i class="ti ti-login"></i>
                                    {{ $attendance->check_in_time
                                        ? \Carbon\Carbon::parse($attendance->check_in_time)->format('H:i')
                                        : '--:--' }}
                                </div>
                            </td>

                            <td>
                                <div class="time-chip out">
                                    <i class="ti ti-logout"></i>
                                    {{ $attendance->check_out_time
                                        ? \Carbon\Carbon::parse($attendance->check_out_time)->format('H:i')
                                        : '--:--' }}
                                </div>
                            </td>

                            <td class="work-hours">
                                @if($attendance->status === 'working')
                                    <span class="hours-dash">--</span>
                                @else
                                    {{ floor(($attendance->total_work_minutes ?? 0) / 60) }}h
                                    {{ ($attendance->total_work_minutes ?? 0) % 60 }}m
                                @endif
                            </td>

                            <td>
                                @if(($attendance->late_minutes ?? 0) > 0)
                                    <span class="mini-badge warn">
                                        {{ $attendance->late_minutes }} phút
                                    </span>
                                @else
                                    —
                                @endif
                            </td>

                            <td>
                                @if(($attendance->early_leave_minutes ?? 0) > 0)
                                    <span class="mini-badge info">
                                        {{ $attendance->early_leave_minutes }} phút
                                    </span>
                                @else
                                    —
                                @endif
                            </td>

                            <td class="center">
                                <span class="status-badge {{ $class }}">
                                    {{ $text }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">
                                    <i class="ti ti-calendar-off"></i>
                                    <p>Chưa có dữ liệu chấm công.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <!-- PAGINATION -->
        <div class="pagination-wrap">
            {{ $attendances->links() }}
        </div>
    </div>

    <div id="vucci-chatbox">
        <!-- BUTTON -->
        <button id="vucci-chat-toggle">
            💬
        </button>

        <!-- POPUP -->
        <div id="vucci-chat-popup">
            <!-- HEADER -->
            <div id="vucci-chat-header">
                <span>VUCCI Chatbot</span>
                <button id="vucci-chat-close">✕</button>
            </div>

            <!-- BODY -->
            <div id="vucci-chat-body">
                <iframe
                    src="https://chat-bot-ai-gamma.vercel.app/"
                    allow="microphone"
                ></iframe>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        body {
            background: #eef2f7;
        }

        .history-shell {
            max-width: 1180px;
            margin: 0 auto;
            padding: 28px;
            color: #172033;
        }

        .history-hero {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 20px;
            margin-bottom: 22px;
        }

        .eyebrow {
            margin: 0 0 6px;
            color: #2563eb;
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        .history-hero h1 {
            margin: 0;
            color: #111827;
            font-size: 34px;
            font-weight: 900;
        }

        .history-hero span {
            color: #64748b;
            font-size: 14px;
        }

        .hero-card {
            min-width: 180px;
            padding: 18px 22px;
            border-radius: 20px;
            background: #0f172a;
            color: white;
            box-shadow: 0 18px 50px rgba(15,23,42,.18);
        }

        .hero-card span {
            color: #cbd5e1;
            font-size: 13px;
        }

        .hero-card strong {
            display: block;
            margin-top: 6px;
            font-size: 34px;
            font-weight: 900;
        }

        .panel {
            background: rgba(255,255,255,.95);
            border: 1px solid #e2e8f0;
            border-radius: 24px;
            box-shadow: 0 14px 38px rgba(15,23,42,.07);
        }

        .history-panel {
            padding: 22px;
        }

        .panel-head {
            margin-bottom: 18px;
        }

        .panel-head h2 {
            margin: 0;
            font-size: 24px;
            font-weight: 800;
            color: #111827;
        }

        .table-wrap {
            overflow-x: auto;
        }

        .history-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 960px;
        }

        .history-table thead th {
            background: #f8fafc;
            color: #64748b;
            font-size: 13px;
            font-weight: 800;
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }

        .history-table tbody td {
            padding: 16px 15px;
            border-bottom: 1px solid #edf2f7;
            vertical-align: middle;
        }

        .date-cell {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            background: #eff6ff;
            color: #1d4ed8;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .date-cell strong {
            font-size: 22px;
            line-height: 1;
        }

        .date-cell span {
            margin-top: 4px;
            font-size: 11px;
        }

        .shift-name {
            font-size: 13px;
            font-weight: 700;
            color: #1d4ed8;
            background: #eff6ff;
            padding: 6px 12px;
            border-radius: 999px;
            white-space: nowrap;
        }

        .time-chip {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 10px 14px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 700;
        }

        .time-chip.in {
            background: #dcfce7;
            color: #047857;
        }

        .time-chip.out {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .work-hours {
            font-weight: 900;
            color: #111827;
        }

        .hours-dash {
            color: #94a3b8;
            font-weight: 700;
        }

        .status-badge,
        .mini-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 800;
        }

        .status-badge.ok {
            background: #d1fae5;
            color: #047857;
        }

        .status-badge.warn,
        .mini-badge.warn {
            background: #fef3c7;
            color: #b45309;
        }

        .status-badge.info,
        .mini-badge.info {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .status-badge.bad {
            background: #fee2e2;
            color: #b91c1c;
        }

        .status-badge.muted {
            background: #f1f5f9;
            color: #64748b;
        }

        .center {
            text-align: center;
        }

        .empty-state {
            min-height: 180px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 12px;
            color: #64748b;
        }

        .empty-state i {
            font-size: 44px;
            color: #94a3b8;
        }

        .pagination-wrap {
            margin-top: 22px;
        }

        .status-badge.working {
            background: #d1fae5;
            color: #047857;
            animation: pulse 1.8s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50%       { opacity: .55; }
        }

        @media (max-width: 768px) {
            .history-shell {
                padding: 18px;
            }
            .history-hero {
                flex-direction: column;
                align-items: stretch;
            }
            .hero-card {
                width: 100%;
            }
            .history-hero h1 {
                font-size: 28px;
            }
        }

        /* ======================== AI ======================== */
        #vucci-chatbox {
            position: fixed;
            right: 20px;
            bottom: 20px;
            z-index: 999999;
            font-family: Arial, sans-serif;
        }

        #vucci-chat-toggle {
            width: 60px;
            height: 60px;
            border: none;
            border-radius: 50%;
            background: #047857;
            color: #fff;
            font-size: 26px;
            cursor: pointer;
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
            transition: 0.3s;
        }

        #vucci-chat-toggle:hover {
            transform: scale(1.05);
        }

        #vucci-chat-popup {
            width: 380px;
            height: 550px;
            border-radius: 22px;
            overflow: hidden;
            position: absolute;
            right: 0;
            bottom: 20px;
            display: none;
            flex-direction: column;
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
        }

        #vucci-chat-header {
            height: 60px;
            background: #047857;
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            font-size: 20px;
            font-weight: 700;
            flex-shrink: 0;
        }

        #vucci-chat-close {
            background: transparent;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
        }

        #vucci-chat-body {
            flex: 1;
            overflow: hidden;
        }

        #vucci-chat-body iframe {
            width: 100%;
            height: 120%;
            border: none;
            display: block;
            padding-bottom: 50px;
        }

        @media (max-width: 768px) {
            #vucci-chat-popup {
                width: 95vw;
                height: 85vh;
                right: 0;
                bottom: 75px;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        const vucciToggle = document.getElementById("vucci-chat-toggle");
        const vucciPopup  = document.getElementById("vucci-chat-popup");
        const vucciClose  = document.getElementById("vucci-chat-close");

        vucciToggle.addEventListener("click", () => {
            vucciPopup.style.display  = "flex";
            vucciToggle.style.display = "none";
        });

        vucciClose.addEventListener("click", () => {
            vucciPopup.style.display  = "none";
            vucciToggle.style.display = "block";
        });
    </script>
@endpush