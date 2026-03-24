<header class="topbar" id="appTopbar">

    {{-- ── Left: toggle + page title ── --}}
    <div class="topbar-left">

        {{-- Hamburger (visible on mobile/tablet) --}}
        <button class="sidebar-toggle" id="sidebarToggleBtn" aria-label="Toggle sidebar">
            <svg viewBox="0 0 24 24">
                <line x1="3" y1="6" x2="21" y2="6"/>
                <line x1="3" y1="12" x2="21" y2="12"/>
                <line x1="3" y1="18" x2="21" y2="18"/>
            </svg>
        </button>

        {{-- Page title + breadcrumb --}}
        <div>
            <div class="topbar-page-title">{{ $header ?? 'Dashboard' }}</div>
            <div class="topbar-breadcrumb">
                <a href="{{ route('admin.users.index') }}">Beranda</a>
                @isset($header)
                    <span class="sep">/</span>
                    <span>{{ $header }}</span>
                @endisset
            </div>
        </div>
    </div>

    {{-- ── Right: actions + user ── --}}
    <div class="topbar-right">

        {{-- Search button --}}
        <button class="topbar-btn" title="Cari" aria-label="Cari">
            <svg viewBox="0 0 24 24">
                <circle cx="11" cy="11" r="8"/>
                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
        </button>

        {{-- Notification bell --}}
        <button class="topbar-btn" title="Notifikasi" aria-label="Notifikasi">
            <svg viewBox="0 0 24 24">
                <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                <path d="M13.73 21a2 2 0 01-3.46 0"/>
            </svg>
            <span class="notif-dot"></span>
        </button>

        {{-- User pill --}}
        <div class="topbar-user">
            <div class="topbar-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</div>
            <div>
                <div class="topbar-user-name">{{ Str::limit(Auth::user()->name, 18) }}</div>
            </div>
            <span class="topbar-user-role">{{ Auth::user()->role }}</span>
        </div>

        {{-- Logout (desktop only shortcut) --}}
        <form method="POST" action="{{ route('logout') }}" style="display:none" id="topbarLogoutForm">@csrf</form>

    </div>

</header>