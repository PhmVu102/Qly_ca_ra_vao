<nav x-data="{ open: false, userOpen: false }">
    <div class="nav-inner">

        <!-- LOGO -->
        <a class="nav-logo"
           href="{{ Auth::user()?->isStaff() ? route('staff.dashboard') : route('dashboard') }}">
            <i class="ti ti-fingerprint"></i>
            <span>HUNONIC</span>
        </a>

        <!-- LINKS — desktop -->
        <div class="nav-links">
            @if(Auth::user()?->isStaff())
                <a href="{{ route('staff.dashboard') }}"
                   class="nav-link {{ request()->routeIs('staff.dashboard') ? 'active' : '' }}">
                    <i class="ti ti-layout-dashboard"></i> Chấm công
                </a>
                <a href="{{ route('staff.history') }}"
                   class="nav-link {{ request()->routeIs('staff.history') ? 'active' : '' }}">
                    <i class="ti ti-history"></i> Lịch sử
                </a>

            @else
                <a href="{{ route('dashboard') }}"
                   class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="ti ti-layout-dashboard"></i> Dashboard
                </a>
            @endif
        </div>

        <!-- USER — desktop -->
        <div class="nav-user">
            <div class="user-pill" @click="userOpen = !userOpen">
                <div class="user-avatar">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <span>{{ Auth::user()->name }}</span>
                <i class="ti ti-chevron-down" :class="{ 'rotated': userOpen  }"></i>
            </div>

            <div class="user-dropdown" x-show="userOpen" x-cloak @click.outside="userOpen = false"
                 x-transition:enter="dropdown-enter"
                 x-transition:enter-start="dropdown-enter-start"
                 x-transition:enter-end="dropdown-enter-end">

                <div class="dropdown-info">
                    <strong>{{ Auth::user()->name }}</strong>
                    <span>{{ Auth::user()->email }}</span>
                </div>

                <div class="dropdown-divider"></div>

                <a href="{{ route('profile.edit') }}" class="dropdown-item">
                    <i class="ti ti-user"></i> Hồ sơ cá nhân
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item logout">
                        <i class="ti ti-logout"></i> Đăng xuất
                    </button>
                </form>
            </div>
        </div>

        <!-- HAMBURGER — mobile -->
        <button class="hamburger" @click="open = !open">
            <i class="ti" :class="open ? 'ti-x' : 'ti-menu-2'"></i>
        </button>
    </div>

    <!-- MOBILE MENU -->
    <div class="mobile-menu" :class="{ 'open': open }" x-cloak>

        {{-- ✅ User info lên trên cùng --}}
        <a href="{{ route('profile.edit') }}" class="mobile-user">
            <div class="user-avatar lg">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
            <div>
                <strong>{{ Auth::user()->name }}</strong>
                <span>{{ Auth::user()->email }}</span>
            </div>
            <i class="ti ti-chevron-right" style="margin-left:auto; color:rgba(255,255,255,.4);"></i>
        </a>

        <div class="mobile-divider"></div>

        {{-- Links --}}
        <div class="mobile-links">
            @if(Auth::user()?->isStaff())
                <a href="{{ route('staff.dashboard') }}"
                class="mobile-link {{ request()->routeIs('staff.dashboard') ? 'active' : '' }}">
                    <i class="ti ti-layout-dashboard"></i> Chấm công
                </a>
                <a href="{{ route('staff.history') }}"
                class="mobile-link {{ request()->routeIs('staff.history') ? 'active' : '' }}">
                    <i class="ti ti-history"></i> Lịch sử
                </a>
            @else
                <a href="{{ route('dashboard') }}"
                class="mobile-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="ti ti-layout-dashboard"></i> Dashboard
                </a>
            @endif
        </div>

        <div class="mobile-divider"></div>

        {{-- Actions --}}
        <div class="mobile-actions">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="mobile-link logout">
                    <i class="ti ti-logout"></i> Đăng xuất
                </button>
            </form>
        </div>

    </div>
</nav>

<style>
    nav {
        background: linear-gradient(135deg, #059669, #047857);
        position: sticky;
        top: 0;
        z-index: 1000;
        box-shadow: 0 4px 24px rgba(15,23,42,.18);
    }

    .nav-inner {
        max-width: 1180px;
        margin: 0 auto;
        padding: 0 24px;
        height: 62px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* LOGO */
    .nav-logo {
        display: flex;
        align-items: center;
        gap: 9px;
        text-decoration: none;
        color: white;
        font-size: 17px;
        font-weight: 900;
        letter-spacing: -.01em;
        margin-right: 18px;
        flex-shrink: 0;
    }

    .nav-logo i {
        font-size: 22px;
        color: #a7f3d0;
    }

    /* LINKS */
    .nav-links {
        display: flex;
        align-items: center;
        gap: 2px;
        flex: 1;
        overflow: hidden;
        flex-wrap: nowrap;
        min-width: 0;
    }

    .nav-link {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 8px 14px;
        border-radius: 10px;
        color: rgba(255,255,255,.8);
        font-size: 14px;
        font-weight: 700;
        text-decoration: none;
        transition: .18s;
    }

    .nav-link:hover {
        background: rgba(255,255,255,.15);
        color: white;
    }

    .nav-link.active {
        background: rgba(255,255,255,.18);
        color: #ffffff;
    }

    /* USER */
    .nav-user {
        position: relative;
        flex-shrink: 0;
    }

    .user-pill {
        display: flex;
        align-items: center;
        gap: 9px;
        padding: 6px 12px 6px 6px;
        border-radius: 999px;
        background: rgba(255,255,255,.07);
        cursor: pointer;
        color: white;
        font-size: 14px;
        font-weight: 700;
        transition: .18s;
        user-select: none;
    }

    .user-pill:hover {
        background: rgba(255,255,255,.12);
    }

    .user-pill .ti-chevron-down {
        font-size: 14px;
        color: rgba(255,255,255,.6);
        transition: transform .2s;
    }

    .user-pill .ti-chevron-down.rotated {
        transform: rotate(180deg);
    }

    .user-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: rgba(255,255,255,.2);
        color: white;
        font-size: 13px;
        font-weight: 900;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .user-avatar.lg {
        width: 42px;
        height: 42px;
        font-size: 16px;
    }

    /* DROPDOWN */
    .user-dropdown {
        position: absolute;
        top: calc(100% + 10px);
        right: 0;
        width: 220px;
        background: white;
        border-radius: 16px;
        box-shadow: 0 20px 60px rgba(15,23,42,.2);
        overflow: hidden;
        border: 1px solid #e2e8f0;
    }

    .dropdown-info {
        padding: 14px 16px;
        background: #f8fafc;
    }

    .dropdown-info strong {
        display: block;
        font-size: 14px;
        font-weight: 800;
        color: #111827;
    }

    .dropdown-info span {
        display: block;
        font-size: 12px;
        color: #64748b;
        margin-top: 2px;
    }

    .dropdown-divider {
        height: 1px;
        background: #e2e8f0;
    }

    .dropdown-item {
        display: flex;
        align-items: center;
        gap: 10px;
        width: 100%;
        padding: 11px 16px;
        font-size: 14px;
        font-weight: 700;
        color: #374151;
        text-decoration: none;
        background: none;
        border: none;
        cursor: pointer;
        transition: .15s;
        text-align: left;
    }

    .dropdown-item:hover {
        background: #f1f5f9;
        color: #111827;
    }

    .dropdown-item.logout {
        color: #dc2626;
    }

    .dropdown-item.logout:hover {
        background: #fff1f2;
    }

    /* HAMBURGER */
    .hamburger {
        display: none;
        width: 38px;
        height: 38px;
        border: none;
        border-radius: 10px;
        background: rgba(255,255,255,.07);
        color: white;
        font-size: 20px;
        cursor: pointer;
        align-items: center;
        justify-content: center;
        margin-left: auto;
    }

    /* MOBILE MENU */
    .mobile-menu {
        display: none;
        flex-direction: column;
        background: linear-gradient(180deg, #059669, #065f46);
        border-top: 1px solid rgba(255,255,255,.07);
        padding: 12px 16px 20px;
        gap: 4px;
    }

    .mobile-menu.open {
        display: flex;
    }

    .mobile-links, .mobile-actions {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .mobile-link {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 11px 14px;
        border-radius: 12px;
        color: rgba(255,255,255,.8);
        font-size: 14px;
        font-weight: 700;
        text-decoration: none;
        background: none;
        border: none;
        cursor: pointer;
        width: 100%;
        text-align: left;
        transition: .15s;
    }

    .mobile-link:hover, .mobile-link.active {
        background: rgba(255,255,255,.07);
        color: white;
    }

    .mobile-link.active {
        color: #ffffff;
    }

    .mobile-link.logout { color: #f87171; }
    .mobile-link.logout:hover { background: rgba(248,113,113,.1); }

    .mobile-user {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px;
        margin: 10px 0;
        background: rgba(255,255,255,.05);
        border-radius: 14px;
    }

    .mobile-user strong {
        display: block;
        color: white;
        font-size: 14px;
        font-weight: 800;
    }

    .mobile-user span {
        display: block;
        color: rgba(255,255,255,.6);
        font-size: 12px;
        margin-top: 2px;
    }
    a.mobile-user {
        text-decoration: none;
        transition: .18s;
    }

    a.mobile-user:hover {
        background: rgba(255,255,255,.1);
        border-radius: 14px;
    }

    [x-cloak] { display: none !important; }
    /* RESPONSIVE */
    @media (max-width: 1024px) {
        .nav-links, .nav-user { display: none; }
        .hamburger { display: flex; }
    }

    @media (min-width: 1025px) {
        .mobile-menu { display: none !important; }
    }
</style>
