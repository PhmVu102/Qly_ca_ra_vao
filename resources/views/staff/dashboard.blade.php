@extends('layouts.app')

@section('title', 'Staff Dashboard')

@section('content')
    @php
        $user = Auth::user();
        $hasCheckedIn  = $todayAttendance && $todayAttendance->check_in_time;
        $hasCheckedOut = !$todayAttendance && $lastAttendance && $lastAttendance->check_out_time;
        $allDone       = $hasCheckedOut && !$hasMoreShifts;
    @endphp

    <div class="staff-shell">
        <section class="staff-hero">
            <div>
                <p>Chấm công vào/ra ca làm việc</p>
                <h1>{{ $user->name }}</h1>
                <div class="meta-line">
                    <span><i class="ti ti-id"></i>{{ $user->employee_code ?? 'N/A' }}</span>
                    <span><i class="ti ti-building"></i>{{ $department ?? 'Chưa có phòng ban' }}</span>
                </div>
            </div>
            <div class="clock-card">
                <span id="todayLabel"></span>
                <strong id="liveClock">--:--:--</strong>
            </div>
        </section>

        <section class="work-grid">
            <div class="panel punch-panel">
                <div class="panel-head">
                    <div>
                        <p class="eyebrow">Hôm nay</p>
                        <h2>Vào / ra ca</h2>
                    </div>
                    <div class="gps-pill" id="gpsPill">
                        <span id="gpsDot"></span>
                        <b id="gpsText">Đang lấy GPS</b>
                    </div>
                </div>

                <div class="punch-actions">
                    <article class="punch-card
                    @if($allDone) state-done
                    @elseif($hasCheckedIn) state-working
                    @else state-idle
                    @endif
                ">
                        <div class="punch-top">
                            <div class="punch-icon-wrap">
                                <i class="ti
                                @if($allDone) ti-circle-check
                                @elseif($hasCheckedIn) ti-clock-bolt
                                @else ti-clock-play
                                @endif
                            "></i>
                            </div>
                            <div>
                            <span class="punch-state-label">
                                @if($allDone) Hoàn tất tất cả ca
                                @elseif($hasCheckedIn) Đang làm việc
                                @elseif($hasCheckedOut && $nextShift) Sẵn sàng ca tiếp theo
                                @else Chưa vào ca
                                @endif
                            </span>
                                {{-- Tên ca --}}
                                @if($hasCheckedIn)
                                    <div style="color:rgba(255,255,255,.9);font-size:18px;font-weight:800;margin-top:4px">
                                        {{ $todayAttendance->shift_name ?? '' }}
                                    </div>
                                @elseif($nextShift)
                                    <div style="color:rgba(255,255,255,.9);font-size:18px;font-weight:800;margin-top:4px">
                                        {{ $nextShift->name }}
                                        · {{ \Carbon\Carbon::parse($nextShift->start_time)->format('H:i') }}
                                        – {{ \Carbon\Carbon::parse($nextShift->end_time)->format('H:i') }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="punch-times-row">
                            <div class="punch-time-item">
                                <span><i class="ti ti-login"></i> Vào ca</span>
                                <strong>
                                    @if($hasCheckedIn)
                                        {{ \Carbon\Carbon::parse($todayAttendance->check_in_time)->format('H:i') }}
                                    @else
                                        --:--
                                    @endif
                                </strong>
                            </div>
                            <div class="punch-time-divider">
                                <i class="ti ti-arrow-right"></i>
                            </div>
                            <div class="punch-time-item">
                                <span><i class="ti ti-logout"></i> Ra ca</span>
                                <strong>--:--</strong>
                            </div>
                        </div>

                        @if($allDone)
                            <div class="punch-done-badge">
                                <i class="ti ti-circle-check"></i> Đã hoàn thành tất cả ca hôm nay
                            </div>
                        @elseif($hasCheckedIn)
                            <button class="punch-btn" data-action="checkout">
                                <i class="ti ti-map-pin-check"></i> Ra ca
                            </button>
                        @else
                            <button class="punch-btn" data-action="checkin">
                                <i class="ti ti-map-pin-check"></i> Vào ca
                            </button>
                        @endif
                    </article>
                </div>

                <div class="message-line" id="actionMessage"></div>
            </div>

            <aside class="panel shift-panel">
                <div class="panel-head compact">
                    <div>
                        <p>Ca được phân</p>
                        <h2>Ca hôm nay</h2>
                    </div>
                    <i class="ti ti-calendar-time"></i>
                </div>

                @forelse($todayShifts as $index => $shift)
                    <div class="shift-row">
                        <div class="shift-row__index">{{ $index + 1 }}</div>
                        <div class="shift-row__body">
                            <div class="shift-row__name">{{ $shift->name }}</div>
                            <div class="shift-row__time">
                                <i class="ti ti-clock"></i>
                                {{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }}
                                <i class="ti ti-arrow-right" style="font-size:11px;color:#94a3b8"></i>
                                {{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}
                            </div>
                        </div>
                        <div class="shift-row__tags">
                            <span class="shift-tag">+{{ $shift->late_allow_minutes ?? 0 }}'</span>
                            <span class="shift-tag shift-tag--early">-{{ $shift->early_leave_allow_minutes ?? 0 }}'</span>
                        </div>
                    </div>
                @empty
                    <div class="shift-empty">
                        <i class="ti ti-calendar-off"></i>
                        <span>Hôm nay chưa được phân ca</span>
                    </div>
                @endforelse
            </aside>
        </section>
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

                <button id="vucci-chat-close">
                    ✕
                </button>
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
        /* ===== BASE ===== */
        body { background: #eef2f7; }
        .staff-shell { max-width: 1180px; margin: 0 auto; padding: 28px; color: #172033; background: #eef2f7; }

        /* ===== HERO ===== */
        .staff-hero { display: flex; justify-content: space-between; align-items: flex-end; gap: 20px; margin-bottom: 22px; }
        .staff-hero p { margin: 0 0 6px; color: #64748b; font-size: 14px; }
        .staff-hero h1 { margin: 0; font-size: 34px; font-weight: 800; color: #111827; }
        .meta-line { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px; color: #64748b; font-size: 14px; }
        .meta-line span { display: inline-flex; align-items: center; gap: 6px; }

        .clock-card { min-width: 210px; padding: 18px 20px; border-radius: 18px; background: #0f172a; color: white; box-shadow: 0 18px 50px rgba(15,23,42,.18); }
        .clock-card span { display: block; color: #cbd5e1; font-size: 13px; }
        .clock-card strong { display: block; margin-top: 4px; font-size: 30px; font-variant-numeric: tabular-nums; }

        /* ===== GRID ===== */
        .work-grid { display: grid; grid-template-columns: minmax(0, 1.45fr) minmax(320px, .75fr); gap: 18px; }

        /* ===== PANEL ===== */
        .panel { background: rgba(255,255,255,.95); border: 1px solid #e2e8f0; border-radius: 22px; box-shadow: 0 14px 38px rgba(15,23,42,.07); padding: 22px; }
        .panel h2 { margin: 0; color: #111827; font-weight: 800; }
        .eyebrow { margin: 0 0 6px; color: #2563eb; font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: .08em; }
        .panel-head { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 18px; }
        .panel-head.compact i { color: #2563eb; font-size: 26px; }

        /* ===== GPS PILL ===== */
        .gps-pill { display: inline-flex; align-items: center; gap: 8px; padding: 8px 12px; border-radius: 999px; background: #f8fafc; color: #64748b; font-size: 13px; }
        #gpsDot { width: 9px; height: 9px; border-radius: 50%; background: #94a3b8; flex-shrink: 0; }
        #gpsDot.ok { background: #059669; box-shadow: 0 0 0 4px #d1fae5; }
        #gpsDot.err { background: #dc2626; box-shadow: 0 0 0 4px #fee2e2; }

        /* ===== PUNCH CARD ===== */
        .punch-actions { display: grid; grid-template-columns: 1fr; }

        .punch-card {
            padding: 24px; border-radius: 20px; color: white;
            display: flex; flex-direction: column; gap: 18px;
            position: relative; overflow: hidden;
            transition: background .4s ease;
        }
        .punch-card::before {
            content: ""; position: absolute;
            top: -70px; right: -70px;
            width: 200px; height: 200px;
            border-radius: 50%; background: rgba(255,255,255,.08);
            pointer-events: none;
        }
        .punch-card::after {
            content: ""; position: absolute;
            bottom: -50px; left: -50px;
            width: 140px; height: 140px;
            border-radius: 50%; background: rgba(255,255,255,.05);
            pointer-events: none;
        }

        .punch-card.state-idle    { background: linear-gradient(135deg, #1e40af, #3b82f6); }
        .punch-card.state-working { background: linear-gradient(135deg, #047857, #10b981); }
        .punch-card.state-done    { background: linear-gradient(135deg, #1e3a5f, #1e40af); }

        /* TOP ROW */
        .punch-top {
            display: flex; align-items: center; gap: 14px;
        }
        .punch-icon-wrap {
            width: 48px; height: 48px; border-radius: 16px;
            background: rgba(255,255,255,.18);
            display: flex; align-items: center; justify-content: center;
            font-size: 22px; flex-shrink: 0;
        }
        .punch-state-label {
            font-size: 13px; font-weight: 800;
            text-transform: uppercase; letter-spacing: .08em;
            color: rgba(255,255,255,.8);
        }

        /* TIMES ROW */
        .punch-times-row {
            display: flex; align-items: center; gap: 12px;
        }
        .punch-time-item {
            flex: 1; background: rgba(255,255,255,.13);
            border-radius: 16px; padding: 14px 16px;
            backdrop-filter: blur(4px);
        }
        .punch-time-item span {
            display: flex; align-items: center; gap: 5px;
            font-size: 11px; font-weight: 700;
            text-transform: uppercase; color: rgba(255,255,255,.65);
            margin-bottom: 6px;
        }
        .punch-time-item strong {
            display: block; font-size: 32px; font-weight: 900;
            font-variant-numeric: tabular-nums; color: white; line-height: 1;
        }
        .punch-time-divider {
            color: rgba(255,255,255,.4); font-size: 20px;
            display: flex; align-items: center;
        }

        /* BUTTON */
        .punch-btn {
            width: 100%; border: none; border-radius: 14px;
            padding: 15px 16px;
            background: rgba(255,255,255,.22); color: white;
            font-size: 15px; font-weight: 800; cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            transition: background .2s, transform .1s;
            letter-spacing: .02em;
        }
        .punch-btn:hover { background: rgba(255,255,255,.32); transform: translateY(-1px); }
        .punch-btn:active { transform: translateY(0); }
        .punch-btn:disabled { cursor: wait; opacity: .55; transform: none; }

        /* DONE BADGE */
        .punch-done-badge {
            display: flex; align-items: center; justify-content: center; gap: 8px;
            padding: 15px; border-radius: 14px;
            background: rgba(255,255,255,.15);
            font-size: 14px; font-weight: 800; color: white;
        }

        /* MESSAGE */
        .message-line {
            min-height: 22px; margin-top: 14px;
            font-size: 14px; font-weight: 600;
            border-radius: 10px; padding: 0;
            transition: .2s;
        }
        .message-line.msg-success { background: #d1fae5; color: #065f46; padding: 10px 14px; }
        .message-line.msg-error   { background: #fee2e2; color: #b91c1c; padding: 10px 14px; }
        .message-line.msg-warn    { background: #fef3c7; color: #b45309; padding: 10px 14px; }

        /* ===== SHIFT PANEL ===== */
        .shift-name {
            font-size: 28px;
            font-weight: 900;
            color: #111827;
            margin-bottom: 18px;
            letter-spacing: -0.02em;
            line-height: 1.2;
        }
        .shift-times { display: grid; grid-template-columns: 1fr auto 1fr; align-items: center; gap: 12px; }
        .shift-times div { padding: 16px; background: #f8fafc; border-radius: 16px; }
        .shift-times span { display: block; color: #64748b; font-size: 12px; font-weight: 800; text-transform: uppercase; }
        .shift-times strong { display: block; margin-top: 5px; font-size: 28px; font-weight: 900; color: #111827; }
        .shift-times > i { color: #94a3b8; font-size: 20px; text-align: center; }
        .shift-note { margin-top: 14px; color: #64748b; font-size: 14px; }
        /* ===== SHIFT ROWS ===== */
        .shift-row {
            display: flex; align-items: center; gap: 12px;
            padding: 12px 14px; border-radius: 14px;
            background: #f8fafc; border: 1px solid #e2e8f0;
            margin-bottom: 10px; transition: background .15s;
        }
        .shift-row:last-child { margin-bottom: 0; }
        .shift-row:hover { background: #f1f5f9; }

        .shift-row__index {
            width: 28px; height: 28px; border-radius: 50%;
            background: #2563eb; color: white;
            font-size: 12px; font-weight: 800;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }

        .shift-row__body { flex: 1; min-width: 0; }
        .shift-row__name {
            font-size: 14px; font-weight: 700; color: #111827;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .shift-row__time {
            display: flex; align-items: center; gap: 5px;
            font-size: 13px; color: #475569; margin-top: 3px;
            font-variant-numeric: tabular-nums;
        }

        .shift-row__tags { display: flex; flex-direction: column; gap: 4px; align-items: flex-end; }
        .shift-tag {
            font-size: 11px; font-weight: 700; padding: 2px 8px;
            border-radius: 99px; background: #dbeafe; color: #1d4ed8;
            white-space: nowrap;
        }
        .shift-tag--early { background: #dcfce7; color: #15803d; }

        .shift-empty {
            display: flex; flex-direction: column; align-items: center;
            gap: 8px; padding: 32px 0; color: #94a3b8; font-size: 14px;
        }
        .shift-empty i { font-size: 32px; }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 980px) {
            .staff-shell { padding: 18px; }
            .staff-hero, .work-grid { display: block; }
            .clock-card, .shift-panel { margin-top: 16px; }
        }
        @media (max-width: 640px) {
            .staff-hero h1 { font-size: 28px; }
            .punch-time-item strong { font-size: 26px; }
            #chatbotWrapper { left: 12px; right: 12px; bottom: 12px; }
            #chatbotPopup { width: calc(100vw - 24px); height: 82vh; }
        }

        /* ======================== AI ======================== */
        #vucci-chatbox{
            position: fixed;
            right: 20px;
            bottom: 20px;
            z-index: 999999;
            font-family: Arial, sans-serif;
        }

        #vucci-chat-toggle{
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

        #vucci-chat-toggle:hover{
            transform: scale(1.05);
        }

        #vucci-chat-popup{
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

        #vucci-chat-header{
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

        #vucci-chat-close{
            background: transparent;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
        }

        #vucci-chat-body{
            flex: 1;
            overflow: hidden;
        }

        #vucci-chat-body iframe{
            width: 100%;
            height: 120%;
            border: none;
            display: block;
            padding-bottom: 50px;
        }

        @media(max-width:768px){
            #vucci-chat-popup{
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
        const clock       = document.getElementById('liveClock');
        const todayLabel  = document.getElementById('todayLabel');
        const gpsDot      = document.getElementById('gpsDot');
        const gpsText     = document.getElementById('gpsText');
        const messageLine = document.getElementById('actionMessage');
        let currentPosition = null;

        // ===== CLOCK =====
        function tickClock() {
            clock.textContent = new Date().toLocaleTimeString('vi-VN', {
                hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false
            });
        }
        tickClock();
        setInterval(tickClock, 1000);
        todayLabel.textContent = new Date().toLocaleDateString('vi-VN', {
            weekday: 'long', day: '2-digit', month: '2-digit', year: 'numeric'
        });

        // ===== GPS =====
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                position => {
                    currentPosition = position.coords;
                    gpsDot.classList.remove('err');
                    gpsDot.classList.add('ok');
                    gpsText.textContent = 'GPS sẵn sàng';
                },
                () => {
                    gpsDot.classList.remove('ok');
                    gpsDot.classList.add('err');
                    gpsText.textContent = 'Cần cấp quyền GPS';
                },
                { enableHighAccuracy: true, timeout: 10000 }
            );
        } else {
            gpsDot.classList.add('err');
            gpsText.textContent = 'Trình duyệt không hỗ trợ GPS';
        }

        // ===== CHECK-IN / CHECK-OUT =====
        document.querySelectorAll('[data-action]').forEach(button => {
            button.addEventListener('click', async () => {
                if (!currentPosition) {
                    messageLine.textContent = 'Chưa lấy được GPS. Hãy cấp quyền vị trí và thử lại.';
                    messageLine.className = 'message-line msg-warn';
                    return;
                }

                const action = button.dataset.action;
                const url = action === 'checkin'
                    ? '{{ route('staff.checkin') }}'
                    : '{{ route('staff.checkout') }}';

                button.disabled = true;
                messageLine.textContent = 'Đang xử lý...';
                messageLine.className = 'message-line';

                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            latitude: currentPosition.latitude,
                            longitude: currentPosition.longitude
                        })
                    });

                    const data = await response.json();
                    messageLine.textContent = data.message || 'Đã xử lý yêu cầu.';
                    messageLine.className = 'message-line ' + (data.success ? 'msg-success' : 'msg-error');

                    if (data.success) {
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        button.disabled = false;
                    }
                } catch (error) {
                    messageLine.textContent = 'Không gửi được yêu cầu. Kiểm tra kết nối và thử lại.';
                    messageLine.className = 'message-line msg-error';
                    button.disabled = false;
                }
            });
        });

        // ======================= AI =======================
        const vucciToggle = document.getElementById("vucci-chat-toggle");
        const vucciPopup = document.getElementById("vucci-chat-popup");
        const vucciClose = document.getElementById("vucci-chat-close");

        vucciToggle.addEventListener("click", () => {

            vucciPopup.style.display = "flex";

            vucciToggle.style.display = "none";

        });

        vucciClose.addEventListener("click", () => {

            vucciPopup.style.display = "none";

            vucciToggle.style.display = "block";

        });
    </script>
@endpush
