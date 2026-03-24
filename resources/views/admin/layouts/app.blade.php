<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'HIKARI Logistik') }} — {{ $title ?? 'Dashboard' }}</title>

    <link rel="icon" type="image/png" href="{{ asset('images/teslog.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=syne:400,500,600,700,800&family=dm-sans:300,400,500,600" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* ── CSS VARIABLES ── */
        :root {
            --navy:         #111FA2;
            --navy-mid:     #1a2db5;
            --navy-light:   #2d3fc7;
            --navy-pale:    #e8eaff;
            --navy-xpale:   #f0f2ff;
            --cream:        #FEFFD3;
            --cream-mid:    #f5f6b8;
            --cream-dark:   #e8e9a0;
            --white:        #ffffff;
            --ink:          #111827;
            --ink-mid:      #374151;
            --ink-soft:     #6b7280;
            --ink-faint:    #9ca3af;
            --border:       rgba(17,31,162,0.1);
            --border-soft:  rgba(17,31,162,0.06);
            --bg:           #f8f9ff;
            --sidebar-w:    260px;
            --sidebar-w-sm: 72px;
            --topbar-h:     64px;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { height: 100%; scroll-behavior: smooth; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--ink);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ── LAYOUT SHELL ── */
        .app-shell {
            display: flex;
            min-height: 100vh;
        }

        /* ════════════════════════════════
           SIDEBAR
        ════════════════════════════════ */
        .sidebar {
            width: var(--sidebar-w);
            min-height: 100vh;
            background: var(--navy);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 200;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), width 0.3s ease;
            overflow: hidden;
        }

        /* Subtle grid texture */
        .sidebar::before {
            content: '';
            position: absolute; inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.025) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.025) 1px, transparent 1px);
            background-size: 28px 28px;
            mask-image: linear-gradient(to bottom, transparent, rgba(0,0,0,0.5) 40%, rgba(0,0,0,0.5) 80%, transparent);
            -webkit-mask-image: linear-gradient(to bottom, transparent, rgba(0,0,0,0.5) 40%, rgba(0,0,0,0.5) 80%, transparent);
            pointer-events: none;
        }

        /* Cream glow top-right */
        .sidebar::after {
            content: '';
            position: absolute;
            top: -80px; right: -80px;
            width: 200px; height: 200px;
            background: radial-gradient(circle, rgba(254,255,211,0.06) 0%, transparent 70%);
            pointer-events: none;
        }

        /* ── Sidebar Brand ── */
        .sb-brand {
            height: var(--topbar-h);
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 20px;
            border-bottom: 1px solid rgba(255,255,255,0.07);
            flex-shrink: 0;
            position: relative;
            z-index: 1;
            text-decoration: none;
        }

        .sb-logo-mark {
            width: 36px; height: 36px;
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.18);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            transition: all 0.25s;
        }
        .sb-brand:hover .sb-logo-mark {
            background: rgba(254,255,211,0.15);
            border-color: rgba(254,255,211,0.25);
        }
        .sb-logo-mark svg { width: 18px; height: 18px; stroke: #fff; fill: none; stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round; }

        .sb-brand-text { overflow: hidden; white-space: nowrap; }
        .sb-brand-name {
            font-family: 'Syne', sans-serif;
            font-size: 0.95rem; font-weight: 800;
            color: #fff; letter-spacing: -0.01em;
            line-height: 1.15;
        }
        .sb-brand-name em { font-style: normal; color: var(--cream); }
        .sb-brand-sub { font-size: 0.62rem; color: rgba(255,255,255,0.4); letter-spacing: 0.04em; }

        /* ── Sidebar Nav ── */
        .sb-nav {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 16px 0;
            position: relative; z-index: 1;
            scrollbar-width: none;
        }
        .sb-nav::-webkit-scrollbar { display: none; }

        .sb-section-label {
            font-size: 0.58rem;
            font-weight: 700;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: rgba(255,255,255,0.25);
            padding: 8px 20px 4px;
            white-space: nowrap;
            overflow: hidden;
        }

        .sb-link {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 10px 20px;
            margin: 1px 8px;
            border-radius: 10px;
            text-decoration: none;
            color: rgba(255,255,255,0.6);
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s ease;
            position: relative;
            white-space: nowrap;
            overflow: hidden;
        }

        .sb-link:hover {
            color: #fff;
            background: rgba(255,255,255,0.07);
        }

        .sb-link.active {
            color: var(--navy);
            background: var(--cream);
            font-weight: 600;
            box-shadow: 0 2px 12px rgba(254,255,211,0.2);
        }

        .sb-link.active .sb-icon { stroke: var(--navy); }

        .sb-link.active::before {
            content: '';
            position: absolute;
            left: 0; top: 20%; bottom: 20%;
            width: 3px;
            background: var(--navy);
            border-radius: 0 3px 3px 0;
        }

        .sb-icon {
            width: 18px; height: 18px;
            stroke: currentColor;
            fill: none;
            stroke-width: 1.8;
            stroke-linecap: round; stroke-linejoin: round;
            flex-shrink: 0;
        }

        .sb-link-label { flex: 1; overflow: hidden; text-overflow: ellipsis; }

        .sb-badge {
            font-size: 0.6rem; font-weight: 700;
            padding: 2px 7px;
            border-radius: 100px;
            background: rgba(254,255,211,0.15);
            color: var(--cream);
            border: 1px solid rgba(254,255,211,0.2);
            flex-shrink: 0;
        }

        .sb-badge.red {
            background: rgba(239,68,68,0.2);
            color: #fca5a5;
            border-color: rgba(239,68,68,0.3);
        }

        /* ── Sidebar Divider ── */
        .sb-divider {
            height: 1px;
            background: rgba(255,255,255,0.06);
            margin: 8px 16px;
        }

        /* ── Sidebar Footer (user info) ── */
        .sb-footer {
            position: relative; z-index: 1;
            padding: 12px 12px 16px;
            border-top: 1px solid rgba(255,255,255,0.07);
            flex-shrink: 0;
        }

        .sb-user {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 10px;
            border-radius: 12px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.08);
            text-decoration: none;
            transition: all 0.2s;
        }

        .sb-user:hover { background: rgba(255,255,255,0.09); }

        .sb-avatar {
            width: 34px; height: 34px;
            border-radius: 9px;
            background: linear-gradient(135deg, var(--navy-mid), var(--navy-light));
            border: 1px solid rgba(254,255,211,0.2);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            font-family: 'Syne', sans-serif;
            font-size: 0.8rem;
            font-weight: 800;
            color: var(--cream);
        }

        .sb-user-info { flex: 1; overflow: hidden; min-width: 0; }
        .sb-user-name {
            font-size: 0.8rem; font-weight: 600;
            color: #fff;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .sb-user-role {
            font-size: 0.62rem;
            color: rgba(255,255,255,0.4);
            text-transform: capitalize;
        }

        .sb-logout-btn {
            width: 30px; height: 30px;
            border-radius: 8px;
            background: rgba(239,68,68,0.1);
            border: 1px solid rgba(239,68,68,0.2);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            flex-shrink: 0;
        }
        .sb-logout-btn:hover { background: rgba(239,68,68,0.25); border-color: rgba(239,68,68,0.4); }
        .sb-logout-btn svg { width: 14px; height: 14px; stroke: #fca5a5; fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }

        /* ════════════════════════════════
           TOPBAR
        ════════════════════════════════ */
        .topbar {
            position: fixed;
            top: 0;
            left: var(--sidebar-w);
            right: 0;
            height: var(--topbar-h);
            background: rgba(248,249,255,0.9);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border-soft);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 clamp(16px, 3vw, 32px);
            z-index: 100;
            transition: left 0.3s ease;
            gap: 16px;
        }

        /* ── Topbar Left ── */
        .topbar-left { display: flex; align-items: center; gap: 14px; min-width: 0; flex: 1; }

        /* Hamburger (mobile only) */
        .sidebar-toggle {
            display: none;
            width: 38px; height: 38px;
            border: 1px solid var(--border);
            border-radius: 9px;
            background: var(--white);
            cursor: pointer;
            align-items: center; justify-content: center;
            transition: all 0.2s;
            flex-shrink: 0;
        }
        .sidebar-toggle:hover { background: var(--navy-xpale); border-color: var(--border); }
        .sidebar-toggle svg { width: 18px; height: 18px; stroke: var(--ink-mid); fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }

        /* Page title */
        .topbar-page-title {
            font-family: 'Syne', sans-serif;
            font-size: 1.05rem; font-weight: 700;
            color: var(--ink);
            letter-spacing: -0.01em;
            white-space: nowrap;
            overflow: hidden; text-overflow: ellipsis;
        }

        .topbar-breadcrumb {
            display: flex; align-items: center; gap: 5px;
            font-size: 0.75rem; color: var(--ink-soft);
            margin-top: 1px;
            white-space: nowrap;
        }
        .topbar-breadcrumb a { color: var(--navy); text-decoration: none; }
        .topbar-breadcrumb a:hover { text-decoration: underline; }
        .topbar-breadcrumb .sep { color: var(--ink-faint); }

        /* ── Topbar Right ── */
        .topbar-right { display: flex; align-items: center; gap: 10px; flex-shrink: 0; }

        /* Notification bell */
        .topbar-btn {
            width: 38px; height: 38px;
            border: 1px solid var(--border-soft);
            border-radius: 9px;
            background: var(--white);
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            transition: all 0.2s;
            position: relative;
            text-decoration: none;
        }
        .topbar-btn:hover { background: var(--navy-xpale); border-color: var(--border); }
        .topbar-btn svg { width: 16px; height: 16px; stroke: var(--ink-soft); fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
        .topbar-btn:hover svg { stroke: var(--navy); }

        .notif-dot {
            position: absolute;
            top: 7px; right: 7px;
            width: 7px; height: 7px;
            background: #ef4444;
            border-radius: 50%;
            border: 1.5px solid var(--white);
            box-shadow: 0 0 6px rgba(239,68,68,0.5);
        }

        /* User pill */
        .topbar-user {
            display: flex;
            align-items: center;
            gap: 9px;
            background: var(--white);
            border: 1px solid var(--border-soft);
            border-radius: 10px;
            padding: 5px 12px 5px 5px;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
        }
        .topbar-user:hover { border-color: var(--border); box-shadow: 0 2px 8px rgba(17,31,162,0.06); }

        .topbar-avatar {
            width: 30px; height: 30px;
            border-radius: 7px;
            background: var(--navy);
            display: flex; align-items: center; justify-content: center;
            font-family: 'Syne', sans-serif;
            font-size: 0.7rem; font-weight: 800;
            color: var(--cream);
            flex-shrink: 0;
        }

        .topbar-user-name { font-size: 0.8rem; font-weight: 600; color: var(--ink); white-space: nowrap; }
        .topbar-user-role {
            display: inline-block;
            font-size: 0.58rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.06em;
            color: var(--navy);
            background: var(--navy-xpale);
            border: 1px solid var(--border);
            border-radius: 100px;
            padding: 1px 7px;
        }

        /* ════════════════════════════════
           MAIN CONTENT AREA
        ════════════════════════════════ */
        .main-wrapper {
            margin-left: var(--sidebar-w);
            margin-top: var(--topbar-h);
            flex: 1;
            min-height: calc(100vh - var(--topbar-h));
            transition: margin-left 0.3s ease;
        }

        .page-content {
            padding: clamp(20px, 3vw, 32px);
            max-width: 1400px;
        }

        /* ════════════════════════════════
           SIDEBAR OVERLAY (mobile)
        ════════════════════════════════ */
        .sidebar-overlay {
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.45);
            z-index: 190;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
            backdrop-filter: blur(2px);
        }
        .sidebar-overlay.active {
            opacity: 1;
            pointer-events: all;
        }

        /* ════════════════════════════════
           RESPONSIVE BREAKPOINTS
        ════════════════════════════════ */

        /* ── Large tablet / small desktop: collapsed sidebar ── */
        @media (max-width: 1180px) {
            :root { --sidebar-w: var(--sidebar-w-sm); }

            .sb-brand-text,
            .sb-section-label,
            .sb-link-label,
            .sb-badge,
            .sb-user-info { display: none; }

            .sb-link { padding: 10px; margin: 1px 8px; justify-content: center; }
            .sb-brand { justify-content: center; padding: 0 10px; }
            .sb-footer { padding: 10px 8px 14px; }
            .sb-user { justify-content: center; padding: 8px; }
            .sb-logout-btn { display: none; }

            .sb-link.active::before { display: none; }

            /* Tooltip on icon hover */
            .sb-link { position: relative; }
            .sb-link::after {
                content: attr(data-label);
                position: absolute;
                left: calc(100% + 10px);
                top: 50%; transform: translateY(-50%);
                background: var(--ink);
                color: #fff;
                font-size: 0.75rem;
                font-weight: 500;
                padding: 5px 10px;
                border-radius: 7px;
                white-space: nowrap;
                pointer-events: none;
                opacity: 0;
                transition: opacity 0.15s;
                z-index: 999;
                box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            }
            .sb-link:hover::after { opacity: 1; }

            .topbar { left: var(--sidebar-w); }
            .main-wrapper { margin-left: var(--sidebar-w); }
        }

        /* ── Mobile: sidebar off-canvas ── */
        @media (max-width: 768px) {
            :root { --sidebar-w: 260px; }

            .sidebar {
                transform: translateX(-100%);
                width: 260px;
            }
            .sidebar.open { transform: translateX(0); }

            .sidebar-toggle { display: flex; }

            /* Restore labels on mobile when sidebar opens */
            .sidebar.open .sb-brand-text,
            .sidebar.open .sb-section-label,
            .sidebar.open .sb-link-label,
            .sidebar.open .sb-badge,
            .sidebar.open .sb-user-info { display: block; }

            .sidebar.open .sb-link { padding: 10px 20px; margin: 1px 8px; justify-content: flex-start; }
            .sidebar.open .sb-brand { justify-content: flex-start; padding: 0 20px; }
            .sidebar.open .sb-user { justify-content: flex-start; }
            .sidebar.open .sb-user-info { display: block; }
            .sidebar.open .sb-logout-btn { display: flex; }
            .sidebar.open .sb-link.active::before { display: block; }
            .sidebar.open .sb-link::after { display: none; }

            .topbar { left: 0; }
            .main-wrapper { margin-left: 0; }
        }

        @media (max-width: 480px) {
            .topbar-user-name { display: none; }
            .topbar-user-role { display: none; }
            .topbar-page-title { font-size: 0.9rem; }
        }
    </style>

    @stack('styles')
</head>

<body>

    <!-- Sidebar overlay (mobile) -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="app-shell">

        {{-- ════════ SIDEBAR ════════ --}}
        {{-- @include('admin.layouts.side') --}}

        {{-- ════════ MAIN AREA ════════ --}}
        <div class="main-wrapper" id="mainWrapper">

            {{-- ── TOPBAR ── --}}
            @include('admin.layouts.nav')

            {{-- ── PAGE CONTENT ── --}}
            <main class="page-content">

                {{-- Flash messages --}}
                {{-- @if (session('success'))
                <div class="flash-success" id="flashMsg">
                    <svg viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    {{ session('success') }}
                    <button onclick="document.getElementById('flashMsg').remove()" class="flash-close">×</button>
                </div>
                @endif

                @if (session('error'))
                <div class="flash-error" id="flashMsgErr">
                    <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    {{ session('error') }}
                    <button onclick="document.getElementById('flashMsgErr').remove()" class="flash-close">×</button>
                </div>
                @endif

                {{ $slot }} --}}
            </main>
        </div>
    </div>

    {{-- Flash message styles --}}
    <style>
        .flash-success, .flash-error {
            display: flex; align-items: center; gap: 10px;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 0.875rem; font-weight: 500;
            margin-bottom: 20px;
            animation: slideIn 0.35s ease;
        }
        @keyframes slideIn { from{opacity:0;transform:translateY(-8px)} to{opacity:1;transform:none} }
        .flash-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; }
        .flash-error   { background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; }
        .flash-success svg, .flash-error svg { width: 16px; height: 16px; stroke: currentColor; fill: none; stroke-width: 2; flex-shrink: 0; }
        .flash-close { margin-left: auto; background: none; border: none; cursor: pointer; font-size: 1.1rem; color: currentColor; opacity: 0.5; padding: 0 4px; }
        .flash-close:hover { opacity: 1; }
    </style>

    <script>
        const sidebar = document.getElementById('appSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const toggleBtn = document.getElementById('sidebarToggleBtn');

        function openSidebar() {
            sidebar.classList.add('open');
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeSidebar() {
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        }

        toggleBtn?.addEventListener('click', () => {
            sidebar.classList.contains('open') ? closeSidebar() : openSidebar();
        });

        overlay.addEventListener('click', closeSidebar);

        // Close sidebar on nav link click (mobile)
        document.querySelectorAll('.sb-link').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 768) closeSidebar();
            });
        });
    </script>

    @stack('scripts')
</body>
</html>