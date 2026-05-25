@extends('layouts.app')

@section('title', 'Hồ sơ nhân viên')

@push('head')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:ital,wght@0,400;0,500;0,600&display=swap" rel="stylesheet">
@endpush

@section('content')
    <div class="pf-root">

        {{-- ░░ BACKGROUND BLOBS ░░ --}}
        <div class="pf-blob pf-blob--1"></div>
        <div class="pf-blob pf-blob--2"></div>

        <div class="pf-wrap">

            {{-- ░░ HERO CARD ░░ --}}
            <div class="pf-hero">

                {{-- cover strip --}}
                <div class="pf-hero__cover">
                    <div class="pf-hero__cover-pattern"></div>
                </div>

                {{-- edit btn --}}
                <button class="pf-edit-btn" onclick="openModal()">
                    <i class="ti ti-pencil"></i> Chỉnh sửa
                </button>

                {{-- avatar floats over cover --}}
                <div class="pf-hero__avatar-row">
                    <div class="pf-avatar-ring">
                        <img
                            src="{{ $avatarUrl }}"
                            class="pf-avatar"
                            alt="{{ $user->name }}"
                        >
                        <span class="pf-avatar-status {{ $user->status ? 'pf-avatar-status--on' : 'pf-avatar-status--off' }}"></span>
                    </div>
                </div>

                {{-- text sits cleanly below --}}
                <div class="pf-hero__text">
                    <h1 class="pf-name">{{ $user->name }}</h1>
                    <div class="pf-meta">
                    <span class="pf-chip pf-chip--role">
                        <i class="ti ti-shield-check"></i>
                        {{ $user->role_name ?? 'Nhân viên' }}
                    </span>
                        <span class="pf-chip pf-chip--dept">
                        <i class="ti ti-building"></i>
                        {{ $user->department->name ?? 'Chưa có phòng ban' }}
                    </span>
                        @if($user->status)
                            <span class="pf-chip pf-chip--active"><i class="ti ti-circle-check"></i> Đang hoạt động</span>
                        @else
                            <span class="pf-chip pf-chip--locked"><i class="ti ti-lock"></i> Đã khóa</span>
                        @endif
                    </div>
                    <button class="pf-edit-btn pf-pwd-btn" onclick="openPwdModal()">
                        <i class="ti ti-lock"></i> Đổi mật khẩu
                    </button>
                </div>
            </div>

            {{-- ░░ INFO GRID ░░ --}}
            <div class="pf-grid">

                <div class="pf-card">
                    <div class="pf-card__head">
                        <i class="ti ti-id-badge-2"></i> Thông tin cá nhân
                    </div>
                    <div class="pf-fields">

                        <div class="pf-field">
                            <span class="pf-field__label">Mã nhân viên</span>
                            <span class="pf-field__val">{{ $user->employee_code ?? '—' }}</span>
                        </div>

                        <div class="pf-field">
                            <span class="pf-field__label">Email</span>
                            <span class="pf-field__val">{{ $user->email }}</span>
                        </div>

                        <div class="pf-field">
                            <span class="pf-field__label">Số điện thoại</span>
                            <span class="pf-field__val">{{ $user->phone ?? '—' }}</span>
                        </div>

                        <div class="pf-field">
                            <span class="pf-field__label">Giới tính</span>
                            <span class="pf-field__val">
                            @switch($user->gender)
                                    @case('male')   Nam @break
                                    @case('female') Nữ  @break
                                    @default        Khác
                                @endswitch
                        </span>
                        </div>

                    </div>
                </div>

                <div class="pf-card">
                    <div class="pf-card__head">
                        <i class="ti ti-calendar-event"></i> Thông tin công việc
                    </div>
                    <div class="pf-fields">

                        <div class="pf-field">
                            <span class="pf-field__label">Ngày sinh</span>
                            <span class="pf-field__val">
                            {{ $user->birth_date ? \Carbon\Carbon::parse($user->birth_date)->format('d/m/Y') : '—' }}
                        </span>
                        </div>

                        <div class="pf-field">
                            <span class="pf-field__label">Ngày vào làm</span>
                            <span class="pf-field__val">
                            {{ $user->hire_date ? \Carbon\Carbon::parse($user->hire_date)->format('d/m/Y') : '—' }}
                        </span>
                        </div>

                        <div class="pf-field">
                            <span class="pf-field__label">Trạng thái tài khoản</span>
                            <span class="pf-field__val">
                            @if($user->status)
                                    <span class="pf-badge pf-badge--green">Đang hoạt động</span>
                                @else
                                    <span class="pf-badge pf-badge--red">Đã khóa</span>
                                @endif
                        </span>
                        </div>

                        <div class="pf-field">
                            <span class="pf-field__label">Đăng nhập gần nhất</span>
                            <span class="pf-field__val">
                            {{ $user->last_login_at ? \Carbon\Carbon::parse($user->last_login_at)->format('H:i d/m/Y') : '—' }}
                        </span>
                        </div>

                    </div>
                </div>

            </div>

        </div>
    </div>

    {{-- ░░ MODAL ░░ --}}
    <div id="editModal" class="pf-overlay pf-overlay--hidden" onclick="overlayClose(event)">
        <div class="pf-modal">

            <div class="pf-modal__head">
                <h2 class="pf-modal__title">Chỉnh Sửa Hồ Sơ</h2>
                <button class="pf-modal__close" onclick="closeModal()">
                    <i class="ti ti-x"></i>
                </button>
            </div>

            <form action="{{ route('staff.update') }}" method="POST" enctype="multipart/form-data" class="pf-modal__body">
                @csrf
                @method('PATCH')

                <div class="pf-modal__grid">

                    <div class="pf-input-group">
                        <label>Họ tên</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" placeholder="Nhập họ tên">
                    </div>

                    <div class="pf-input-group">
                        <label>Số điện thoại</label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="Nhập số điện thoại">
                    </div>

                    <div class="pf-input-group">
                        <label>Giới tính</label>
                        <select name="gender">
                            <option value="">-- Chọn --</option>
                            <option value="male"   {{ $user->gender == 'male'   ? 'selected' : '' }}>Nam</option>
                            <option value="female" {{ $user->gender == 'female' ? 'selected' : '' }}>Nữ</option>
                            <option value="other"  {{ $user->gender == 'other'  ? 'selected' : '' }}>Khác</option>
                        </select>
                    </div>

                    <div class="pf-input-group">
                        <label>Ngày sinh</label>
                        <input type="date" name="birth_date" value="{{ old('birth_date', $user->birth_date) }}">
                    </div>

                    <div class="pf-input-group pf-input-group--full">
                        <label>Ảnh đại diện</label>
                        <div class="pf-file-drop">
                            <i class="ti ti-photo-up"></i>
                            <span>Kéo thả hoặc <label for="avatarInput" class="pf-file-link">chọn ảnh</label></span>
                            <input type="file" name="avatar" id="avatarInput" style="display:none" onchange="previewAvatar(this)">
                            <small id="avatarName">JPG, PNG — tối đa 2MB</small>
                        </div>
                    </div>

                </div>

                <div class="pf-modal__foot">
                    <button type="button" onclick="closeModal()" class="pf-btn pf-btn--ghost">Hủy</button>
                    <button type="submit" class="pf-btn pf-btn--primary">
                        <i class="ti ti-device-floppy"></i> Lưu thay đổi
                    </button>
                </div>

            </form>

        </div>
    </div>
    <div id="pwdModal" class="pf-overlay pf-overlay--hidden" onclick="overlayClosePwd(event)">
        <div class="pf-modal">

            <div class="pf-modal__head">
                <h2 class="pf-modal__title">Đổi mật khẩu</h2>
                <button class="pf-modal__close" onclick="closePwdModal()">
                    <i class="ti ti-x"></i>
                </button>
            </div>

            <form action="{{ route('staff.changePassword') }}" method="POST" class="pf-modal__body" autocomplete="off">
                @csrf
                @method('PATCH')

                <div style="display:flex;flex-direction:column;gap:14px;">

                    <div class="pf-input-group">
                        <label>Mật khẩu hiện tại</label>
                        <div class="pf-input-wrap">
                            <input type="password" name="current_password" placeholder="Nhập mật khẩu hiện tại" autocomplete="current-password">
                            <button type="button" class="pf-eye-btn" onclick="togglePassword(this)">
                                <i class="ti ti-eye-off"></i>
                            </button>
                        </div>
                    </div>

                    <div class="pf-input-group">
                        <label>Mật khẩu mới</label>
                        <div class="pf-input-wrap">
                            <input type="password" name="password" placeholder="Tối thiểu 8 ký tự" autocomplete="new-password">
                            <button type="button" class="pf-eye-btn" onclick="togglePassword(this)">
                                <i class="ti ti-eye-off"></i>
                            </button>
                        </div>
                    </div>

                    <div class="pf-input-group">
                        <label>Xác nhận mật khẩu mới</label>
                        <div class="pf-input-wrap">
                            <input type="password" name="password_confirmation" placeholder="Nhập lại mật khẩu mới" autocomplete="new-password">
                            <button type="button" class="pf-eye-btn" onclick="togglePassword(this)">
                                <i class="ti ti-eye-off"></i>
                            </button>
                        </div>
                    </div>

                </div>

                <div class="pf-modal__foot">
                    <button type="button" onclick="closePwdModal()" class="pf-btn pf-btn--ghost">Hủy</button>
                    <button type="submit" class="pf-btn pf-btn--primary">
                        <i class="ti ti-lock-check"></i> Xác nhận đổi
                    </button>
                </div>

            </form>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --orange:     #059669;
            --orange-d:   #047857;
            --orange-bg:  #d1fae5;
            --orange-mid: #a7f3d0;
            --green:      #16a34a;
            --green-bg:   #dcfce7;
            --red:        #dc2626;
            --red-bg:     #fee2e2;
            --ink:        #0f172a;
            --ink2:       #475569;
            --ink3:       #94a3b8;
            --bg:         #f1f5f9;
            --white:      #ffffff;
            --border:     #e2e8f0;
            --font-head: 'Inter', sans-serif;
            --font-body: 'Inter', sans-serif;
            --r: 20px;
        }

        body { background: var(--bg); font-family: var(--font-body); color: var(--ink); }

        /* ── PAGE ── */
        .pf-root {
            min-height: 100vh;
            padding: 2.5rem 1.5rem;
            position: relative;
            overflow: hidden;
        }

        /* blobs */
        .pf-blob {
            position: fixed; border-radius: 50%;
            filter: blur(80px); pointer-events: none; z-index: 0;
        }
        .pf-blob--1 {
            width: 500px; height: 500px;
            background: rgba(249,115,22,.12);
            top: -100px; right: -100px;
        }
        .pf-blob--2 {
            width: 400px; height: 400px;
            background: rgba(249,115,22,.07);
            bottom: -80px; left: -80px;
        }

        .pf-wrap {
            max-width: 1100px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        /* ── HERO ── */
        .pf-hero {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 28px;
            overflow: hidden;
            position: relative;
            box-shadow: 0 4px 32px rgba(0,0,0,.06);
        }

        .pf-hero__cover {
            height: 130px;
            background: linear-gradient(135deg, #059669 0%, #047857 55%, #065f46 100%);
            position: relative;
            overflow: hidden;
        }
        .pf-hero__cover-pattern {
            position: absolute; inset: 0;
            background-image:
                radial-gradient(circle at 20% 50%, rgba(255,255,255,.15) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255,255,255,.1) 0%, transparent 40%);
        }

        .pf-edit-btn {
            position: absolute; top: 1rem; right: 1rem; z-index: 10;
            display: flex; align-items: center; gap: .4rem;
            padding: .5rem 1.1rem; border-radius: 10px;
            background: rgba(255,255,255,.18); backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,.25);
            color: white; font-family: var(--font-body); font-size: .82rem;
            font-weight: 600; cursor: pointer;
            transition: background .15s, transform .1s;
        }
        .pf-edit-btn:hover { background: rgba(255,255,255,.28); transform: translateY(-1px); }

        .pf-hero__avatar-row {
            padding: 0 2rem;
            margin-top: -52px;
            margin-bottom: .75rem;
        }
        .pf-hero__text {
            padding: 0 2rem 1.75rem;
        }

        .pf-avatar-ring {
            position: relative; flex-shrink: 0;
            width: 112px; height: 112px;
            border-radius: 50%;
            background: var(--white);
            padding: 4px;
            box-shadow: 0 0 0 4px var(--orange-mid), 0 8px 24px rgba(0,0,0,.12);
            will-change: transform;
            isolation: isolate;
            overflow: hidden;
            transform: translateZ(0);
            outline: 1px solid transparent;
        }

        .pf-avatar {
            width: 100%; height: 100%;
            clip-path: circle(50%);
            object-fit: cover;
            display: block;
            will-change: transform;
            transform: translateZ(0);
            backface-visibility: hidden;
            -webkit-backface-visibility: hidden;
        }
        .pf-avatar-status {
            position: absolute; bottom: 6px; right: 6px;
            width: 14px; height: 14px; border-radius: 50%;
            border: 2px solid var(--white);
        }
        .pf-avatar-status--on  { background: var(--green); }
        .pf-avatar-status--off { background: var(--red); }


        .pf-name {
            font-size: 1.75rem; font-weight: 800;
            color: var(--ink); line-height: 1.1;
            margin-bottom: .6rem;
        }
        .pf-meta { display: flex; flex-wrap: wrap; gap: .4rem; }

        .pf-chip {
            display: inline-flex; align-items: center; gap: .3rem;
            padding: 4px 12px; border-radius: 99px;
            font-size: .72rem; font-weight: 600;
        }
        .pf-chip--role   { background: var(--orange-bg); color: var(--orange-d); }
        .pf-chip--dept   { background: #f1f5f9; color: var(--ink2); }
        .pf-chip--active { background: var(--green-bg); color: var(--green); }
        .pf-chip--locked { background: var(--red-bg); color: var(--red); }

        /* ── GRID ── */
        .pf-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.25rem;
        }
        @media(max-width: 640px) { .pf-grid { grid-template-columns: 1fr; } }

        /* ── CARDS ── */
        .pf-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 2px 16px rgba(0,0,0,.04);
        }
        .pf-card__head {
            padding: .9rem 1.5rem;
            font-size: .75rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: .07em;
            color: var(--ink2);
            background: var(--bg);
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; gap: .5rem;
        }
        .pf-fields { padding: .5rem 0; }
        .pf-field {
            display: flex; align-items: center; justify-content: space-between;
            padding: .75rem 1.5rem;
            border-bottom: 1px solid #f8fafc;
            gap: 1rem;
            transition: background .12s;
        }
        .pf-field:last-child { border-bottom: none; }
        .pf-field:hover { background: #fafbfc; }
        .pf-field__label { font-size: .78rem; color: var(--ink3); font-weight: 500; flex-shrink: 0; }
        .pf-field__val { font-size: .88rem; font-weight: 600; color: var(--ink); text-align: right; }
        .pf-field__val--mono { font-family: 'DM Mono', 'Courier New', monospace; font-size: .82rem; }

        .pf-badge {
            display: inline-block; padding: 3px 10px;
            border-radius: 99px; font-size: .72rem; font-weight: 700;
        }
        .pf-badge--green { background: var(--green-bg); color: var(--green); }
        .pf-badge--red   { background: var(--red-bg);   color: var(--red); }

        /* ── OVERLAY + MODAL ── */
        .pf-overlay {
            position: fixed; inset: 0; z-index: 100;
            background: rgba(15,23,42,.55);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            overflow-y: auto;
            transition: opacity .2s;
        }

        .pf-overlay--hidden {
            opacity: 0; pointer-events: none;
        }

        .pf-modal {
            background: var(--white);
            border-radius: 24px;
            width: 100%; max-width: 600px;
            overflow: hidden;
            box-shadow: 0 24px 80px rgba(0,0,0,.2);
            transform: translateY(0);
            transition: transform .2s;
            max-height: 90vh;
            overflow-y: auto;
        }
        .pf-overlay--hidden .pf-modal { transform: translateY(20px); }

        .pf-modal__head {
            display: flex; align-items: center; justify-content: space-between;
            padding: 1.25rem 1.75rem;
            border-bottom: 1px solid var(--border);
        }
        .pf-modal__title {
            font-family: var(--font-head);
            font-size: 1.1rem; font-weight: 800; color: var(--ink);
        }
        .pf-modal__close {
            width: 34px; height: 34px; border-radius: 8px;
            border: 1px solid var(--border); background: none;
            display: flex; align-items: center; justify-content: center;
            color: var(--ink2); cursor: pointer; font-size: 1rem;
            transition: background .12s, color .12s;
        }
        .pf-modal__close:hover { background: var(--bg); color: var(--ink); }

        .pf-modal__body { padding: 1.5rem 1.75rem; }

        .pf-modal__grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        @media(max-width: 500px) { .pf-modal__grid { grid-template-columns: 1fr; } }

        .pf-input-group { display: flex; flex-direction: column; gap: .4rem; }
        .pf-input-group--full { grid-column: 1 / -1; }
        .pf-input-group label { font-size: .75rem; font-weight: 600; color: var(--ink2); text-transform: uppercase; letter-spacing: .05em; }

        .pf-input-group input,
        .pf-input-group select {
            padding: .7rem 1rem; border-radius: 12px;
            border: 1.5px solid var(--border);
            font-family: var(--font-body); font-size: .88rem; color: var(--ink);
            background: var(--bg);
            outline: none;
            transition: border-color .15s, box-shadow .15s;
        }
        .pf-input-group input:focus,
        .pf-input-group select:focus {
            border-color: var(--orange);
            box-shadow: 0 0 0 3px rgba(5,150,105,.12);
            background: var(--white);
        }

        /* file drop */
        .pf-file-drop {
            border: 2px dashed var(--border);
            border-radius: 14px;
            padding: 1.25rem;
            display: flex; flex-direction: column; align-items: center; gap: .35rem;
            background: var(--bg);
            cursor: pointer;
            transition: border-color .15s, background .15s;
        }
        .pf-file-drop:hover { border-color: var(--orange); background: var(--orange-bg); }
        .pf-file-drop i { font-size: 1.6rem; color: var(--ink3); }
        .pf-file-drop span { font-size: .82rem; color: var(--ink2); }
        .pf-file-link { color: var(--orange); font-weight: 600; cursor: pointer; text-decoration: underline; }
        .pf-file-drop small { font-size: .72rem; color: var(--ink3); }

        .pf-modal__foot {
            display: flex; justify-content: flex-end; gap: .75rem;
            padding-top: 1.25rem;
            border-top: 1px solid var(--border);
            margin-top: 1.25rem;
        }

        .pf-btn {
            display: inline-flex; align-items: center; gap: .4rem;
            padding: .7rem 1.4rem; border-radius: 12px;
            font-family: var(--font-body); font-size: .88rem; font-weight: 600;
            cursor: pointer; border: none; transition: all .15s;
        }
        .pf-btn--ghost {
            background: var(--bg); color: var(--ink2);
            border: 1.5px solid var(--border);
        }
        .pf-btn--ghost:hover { background: #e2e8f0; }
        .pf-btn--primary {
            background: var(--orange); color: white;
            box-shadow: 0 4px 14px rgba(5,150,105,.35);
        }
        .pf-btn--primary:hover { background: var(--orange-d); transform: translateY(-1px); }
        .pf-btn--primary:active { transform: translateY(0); }
        .pf-pwd-btn {
            position: static;
            margin-top: 14px;
            background: #f1f5f9;
            color: #374151;
            border: 1.5px solid #e2e8f0;
            backdrop-filter: none;
        }
        .pf-pwd-btn:hover { background: #e2e8f0; }
        /* ── VALIDATION ── */
        .pf-input-error {
            border-color: var(--red) !important;
            box-shadow: 0 0 0 3px rgba(220,38,38,.12) !important;
        }
        .pf-field-error {
            font-size: .72rem;
            font-weight: 600;
            color: var(--red);
            margin-top: 2px;
        }
        .pf-input-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }
        .pf-input-wrap input {
            width: 100%;
            padding-right: 2.8rem;
        }
        .pf-eye-btn {
            position: absolute; right: .75rem;
            background: none; border: none;
            color: var(--ink3); cursor: pointer;
            font-size: 1rem; padding: 0;
            display: flex; align-items: center;
            transition: color .15s;
        }
        .pf-eye-btn:hover { color: var(--ink2); }
        #pwdModal input[name="_token"],
        #pwdModal input[name="_method"] {
            display: none !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        function showToast(msg, type = 'success') {
            const toast = document.createElement('div');
            toast.innerHTML = `<i class="ti ti-${type === 'success' ? 'circle-check' : 'alert-circle'}"></i> ${msg}`;
            toast.style.cssText = `
        position: fixed; bottom: 2rem; right: 2rem; z-index: 9999;
        background: ${type === 'success' ? '#059669' : '#dc2626'}; color: white;
        padding: 14px 20px; border-radius: 14px;
        font-family: 'DM Sans', sans-serif; font-size: .88rem; font-weight: 600;
        display: flex; align-items: center; gap: .5rem;
        box-shadow: 0 8px 30px ${type === 'success' ? 'rgba(5,150,105,.35)' : 'rgba(220,38,38,.35)'};
        opacity: 0; transform: translateY(16px);
        transition: opacity .3s, transform .3s;
    `;
            document.body.appendChild(toast);
            requestAnimationFrame(() => {
                toast.style.opacity = '1';
                toast.style.transform = 'translateY(0)';
            });
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(16px)';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        function clearErrors(form) {
            form.querySelectorAll('.pf-field-error').forEach(el => el.remove());
            form.querySelectorAll('.pf-input-error').forEach(el => el.classList.remove('pf-input-error'));
        }

        function showFieldError(el, msg) {
            el.classList.add('pf-input-error');
            const err = document.createElement('span');
            err.className = 'pf-field-error';
            err.textContent = msg;

            const wrapper = el.closest('.pf-input-group');
            if (wrapper) {
                wrapper.appendChild(err);
            } else {
                el.parentNode.appendChild(err);
            }
        }
        function openModal() {
            document.getElementById('editModal').classList.remove('pf-overlay--hidden');
        }
        function closeModal() {
            document.getElementById('editModal').classList.add('pf-overlay--hidden');
            document.querySelector('#editModal form').reset();
            document.getElementById('avatarName').textContent = 'JPG, PNG — tối đa 2MB';
            document.querySelectorAll('#editModal .pf-field-error').forEach(el => el.remove());
            document.querySelectorAll('#editModal .pf-input-error').forEach(el => el.classList.remove('pf-input-error'));
            document.querySelector('[name="birth_date"]').value = '{{ $user->birth_date ?? '' }}';
        }
        function overlayClose(e) {
            if (e.target.id === 'editModal') closeModal();
        }
        function previewAvatar(input) {
            if (input.files[0]) {
                document.getElementById('avatarName').textContent = input.files[0].name;
            }
        }
        function openPwdModal() {
            document.getElementById('pwdModal').classList.remove('pf-overlay--hidden');

            setTimeout(() => {
                document.querySelectorAll('#pwdModal input[type="password"]').forEach(input => {
                    input.type = 'text';
                    input.value = '';
                });
                setTimeout(() => {
                    document.querySelectorAll('#pwdModal input[type="text"]').forEach(input => {
                        input.type = 'password';
                    });
                }, 50);
            }, 200);
        }
        function closePwdModal() {
            document.getElementById('pwdModal').classList.add('pf-overlay--hidden');
            document.querySelector('#pwdModal form').reset();
            document.querySelectorAll('#pwdModal .pf-field-error').forEach(el => el.remove());
            document.querySelectorAll('#pwdModal .pf-input-error').forEach(el => el.classList.remove('pf-input-error'));
            document.querySelectorAll('#pwdModal .pf-eye-btn i').forEach(icon => {
                icon.classList.remove('ti-eye');
                icon.classList.add('ti-eye-off');
            });
            document.querySelectorAll('#pwdModal input').forEach(input => {
                input.type = 'password';
            });
        }
        function overlayClosePwd(e) {
            if (e.target.id === 'pwdModal') closePwdModal();
        }

        // ===== VALIDATE ĐỔI MẬT KHẨU =====
        let isSubmitting = false;

        document.querySelector('#pwdModal form').addEventListener('submit', async function(e) {
            e.preventDefault();

            if (isSubmitting) return; // chặn bấm liên tục

            // Xóa lỗi cũ
            document.querySelectorAll('#pwdModal .pf-field-error').forEach(el => el.remove());
            document.querySelectorAll('#pwdModal .pf-input-error').forEach(el => el.classList.remove('pf-input-error'));

            let hasError = false;

            const current  = this.querySelector('[name="current_password"]');
            const password = this.querySelector('[name="password"]');
            const confirm  = this.querySelector('[name="password_confirmation"]');
            const submitBtn = this.querySelector('[type="submit"]');

            if (!current.value.trim()) {
                showFieldError(current, 'Vui lòng nhập mật khẩu hiện tại');
                hasError = true;
            }

            if (!password.value.trim()) {
                showFieldError(password, 'Vui lòng nhập mật khẩu mới');
                hasError = true;
            } else if (password.value.length < 8) {
                showFieldError(password, 'Mật khẩu mới tối thiểu 8 ký tự');
                hasError = true;
            } else if (password.value.length > 32) {
                showFieldError(password, 'Mật khẩu mới không được vượt quá 32 ký tự');
                hasError = true;
            }

            if (!confirm.value.trim()) {
                showFieldError(confirm, 'Vui lòng xác nhận mật khẩu mới');
                hasError = true;
            } else if (confirm.value !== password.value) {
                showFieldError(confirm, 'Mật khẩu xác nhận không khớp');
                hasError = true;
            }

            if (hasError) return;

            // Khóa nút, đợi AJAX
            isSubmitting = true;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="ti ti-loader"></i> Đang kiểm tra...';

            try {
                const res  = await fetch('{{ route('staff.checkPassword') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ password: current.value })
                });
                const data = await res.json();

                if (!data.correct) {
                    showFieldError(current, 'Mật khẩu hiện tại không đúng');
                    return;
                }

                this.submit();

            } finally {
                // Luôn mở khóa lại dù thành công hay thất bại
                isSubmitting = false;
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="ti ti-lock-check"></i> Xác nhận đổi';
            }
        });
        // ===== VALIDATE CHỈNH SỬA HỒ SƠ =====
        document.querySelector('#editModal form').addEventListener('submit', function(e) {
            document.querySelectorAll('#editModal .pf-field-error').forEach(el => el.remove());
            document.querySelectorAll('#editModal .pf-input-error').forEach(el => el.classList.remove('pf-input-error'));

            let hasError = false;

            const name      = this.querySelector('[name="name"]');
            const phone     = this.querySelector('[name="phone"]');
            const birthDate = this.querySelector('[name="birth_date"]');

            if (!name.value.trim()) {
                showFieldError(name, 'Họ tên không được để trống');
                hasError = true;
            } else if (name.value.trim().length > 100) {
                showFieldError(name, 'Họ tên không được vượt quá 100 ký tự');
                hasError = true;
            } else if (!/^[\p{L}\s]+$/u.test(name.value.trim())) {
                showFieldError(name, 'Họ tên không được chứa số hoặc ký tự đặc biệt');
                hasError = true;
            }

            if (phone.value.trim() && !/^(0[3|5|7|8|9])[0-9]{8}$/.test(phone.value.trim())) {
                showFieldError(phone, 'Số điện thoại không hợp lệ (VD: 0912345678)');
                hasError = true;
            }

            if (birthDate.value) {
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                if (new Date(birthDate.value) >= today) {
                    birthDate.classList.add('pf-input-error');
                    const err = document.createElement('span');
                    err.className = 'pf-field-error';
                    err.textContent = 'Ngày sinh phải trước ngày hiện tại';
                    birthDate.parentNode.appendChild(err);
                    hasError = true;
                }
            }

            if (hasError) e.preventDefault();
        });

        function togglePassword(btn) {
            const input = btn.closest('.pf-input-wrap').querySelector('input');
            const icon  = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('ti-eye-off', 'ti-eye');
            } else {
                input.type = 'password';
                icon.classList.replace('ti-eye', 'ti-eye-off');
            }
        }

        window.addEventListener('DOMContentLoaded', () => {
            @if(session('success_password'))
            showToast('{{ session('success_password') }}', 'success');
            @endif

            @if(session('success'))
            showToast('{{ session('success') }}', 'success');
            @endif

            @if($errors->has('current_password'))
            showToast('{{ $errors->first('current_password') }}', 'error');
            document.getElementById('pwdModal').classList.remove('pf-overlay--hidden');
            @endif

            @if($errors->hasAny(['name', 'phone', 'gender', 'birth_date', 'avatar']))
            document.getElementById('editModal').classList.remove('pf-overlay--hidden');
            @endif
        });
    </script>
@endpush
