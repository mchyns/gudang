<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk | {{ config('app.name', 'HIKARISOU') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=syne:400,500,600,700,800&family=dm-sans:300,400,500,600" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --navy:        #111FA2;
            --navy-mid:    #1a2db5;
            --navy-light:  #2d3fc7;
            --navy-pale:   #e8eaff;
            --navy-xpale:  #f0f2ff;
            --cream:       #FEFFD3;
            --cream-mid:   #f5f6b8;
            --cream-dark:  #e8e9a0;
            --white:       #ffffff;
            --ink:         #111827;
            --ink-mid:     #374151;
            --ink-soft:    #6b7280;
            --ink-faint:   #9ca3af;
            --border:      rgba(17,31,162,0.1);
            --border-soft: rgba(17,31,162,0.06);
            --error:       #dc2626;
            --error-bg:    #fef2f2;
            --error-border:#fecaca;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { height: 100%; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: #f8f9ff;
            color: var(--ink);
            min-height: 100vh;
            display: flex;
            overflow-x: hidden;
        }

        /* ── BACKGROUNDS ── */
        .bg-layer {
            position: fixed; inset: 0; z-index: 0; pointer-events: none;
        }
        .bg-gradient {
            background:
                radial-gradient(ellipse 65% 55% at 100% 0%, rgba(17,31,162,0.07) 0%, transparent 65%),
                radial-gradient(ellipse 50% 45% at 0% 100%, rgba(17,31,162,0.05) 0%, transparent 60%),
                radial-gradient(ellipse 70% 60% at 50% 50%, rgba(254,255,211,0.35) 0%, transparent 80%),
                #f8f9ff;
        }
        .bg-dots {
            background-image: radial-gradient(circle, rgba(17,31,162,0.07) 1px, transparent 1px);
            background-size: 28px 28px;
            opacity: 0.55;
        }

        /* ── LAYOUT SPLIT ── */
        .page {
            position: relative; z-index: 1;
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        /* LEFT PANEL — decorative */
        .panel-left {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 40px 48px;
            background: var(--navy);
            position: relative;
            overflow: hidden;
        }

        /* Left panel texture */
        .panel-left::before {
            content: '';
            position: absolute; inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
            background-size: 40px 40px;
        }

        /* Orbs inside left panel */
        .lp-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            pointer-events: none;
        }
        .lp-orb-1 { width: 380px; height: 380px; top: -80px; right: -80px; background: rgba(254,255,211,0.08); }
        .lp-orb-2 { width: 300px; height: 300px; bottom: -60px; left: -60px; background: rgba(255,255,255,0.05); }

        /* Rings decoration */
        .lp-rings {
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
        }
        .lp-ring {
            position: absolute;
            border-radius: 50%;
            border: 1px solid rgba(255,255,255,0.06);
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
        }
        .lp-ring-1 { width: 440px; height: 440px; animation: lpspin 50s linear infinite; }
        .lp-ring-2 { width: 320px; height: 320px; border-style: dashed; border-color: rgba(254,255,211,0.07); animation: lpspin 35s linear infinite reverse; }
        .lp-ring-3 { width: 200px; height: 200px; animation: lpspin 70s linear infinite; }
        @keyframes lpspin { from{transform:translate(-50%,-50%) rotate(0)} to{transform:translate(-50%,-50%) rotate(360deg)} }

        /* Ring dot */
        .lp-dot {
            position: absolute;
            width: 8px; height: 8px;
            background: var(--cream);
            border-radius: 50%;
            box-shadow: 0 0 12px var(--cream);
            top: -4px; left: 50%;
            transform: translateX(-50%);
        }

        /* Left panel content */
        .lp-brand {
            position: relative; z-index: 1;
            display: flex; align-items: center; gap: 12px;
        }

        .lp-logo {
            width: 42px; height: 42px;
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 11px;
            display: flex; align-items: center; justify-content: center;
            backdrop-filter: blur(8px);
        }
        .lp-logo svg { width: 20px; height: 20px; stroke: #fff; fill: none; stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round; }

        .lp-logo-text .lp-name {
            font-family: 'Syne', sans-serif;
            font-size: 0.95rem; font-weight: 800;
            color: #fff; letter-spacing: -0.01em;
        }
        .lp-logo-text .lp-sub {
            font-size: 0.67rem; color: rgba(255,255,255,0.5);
            letter-spacing: 0.04em;
        }

        /* Central info block */
        .lp-center {
            position: relative; z-index: 1;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .lp-headline {
            font-family: 'Syne', sans-serif;
            font-size: clamp(1.9rem, 2.8vw, 2.8rem);
            font-weight: 800;
            color: #fff;
            line-height: 1.1;
            letter-spacing: -0.025em;
            margin-bottom: 18px;
        }

        .lp-headline .cream-word { color: var(--cream); }

        .lp-desc {
            font-size: 0.9rem;
            color: rgba(255,255,255,0.55);
            line-height: 1.7;
            max-width: 340px;
            font-weight: 300;
            margin-bottom: 40px;
        }

        /* Stats in left panel */
        .lp-stats { display: flex; flex-direction: column; gap: 12px; }

        .lp-stat {
            display: flex; align-items: center; gap: 14px;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 14px;
            padding: 14px 18px;
            backdrop-filter: blur(4px);
        }

        .lp-stat-icon {
            width: 36px; height: 36px; min-width: 36px;
            background: rgba(254,255,211,0.12);
            border: 1px solid rgba(254,255,211,0.15);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
        }
        .lp-stat-icon svg { width: 16px; height: 16px; stroke: var(--cream); fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }

        .lp-stat-val {
            font-family: 'Syne', sans-serif;
            font-size: 1rem; font-weight: 700;
            color: #fff;
        }
        .lp-stat-lbl { font-size: 0.7rem; color: rgba(255,255,255,0.45); margin-top: 1px; }

        /* Left footer */
        .lp-footer {
            position: relative; z-index: 1;
            font-size: 0.72rem;
            color: rgba(255,255,255,0.25);
        }

        /* ── RIGHT PANEL — form ── */
        .panel-right {
            width: 480px;
            min-width: 340px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 48px clamp(28px, 5vw, 52px);
            background: var(--white);
            border-left: 1px solid var(--border-soft);
            position: relative;
        }

        /* subtle top cream bar */
        .panel-right::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--navy), var(--navy-light), transparent);
        }

        .form-wrap { width: 100%; max-width: 380px; }

        /* Form header */
        .form-header { margin-bottom: 36px; }

        .form-badge {
            display: inline-flex; align-items: center; gap: 7px;
            font-size: 0.68rem; font-weight: 700;
            letter-spacing: 0.1em; text-transform: uppercase;
            color: var(--navy);
            background: var(--navy-xpale);
            border: 1px solid var(--border);
            border-radius: 100px;
            padding: 4px 12px;
            margin-bottom: 18px;
        }

        .form-badge-dot {
            width: 5px; height: 5px;
            background: #22c55e;
            border-radius: 50%;
            box-shadow: 0 0 0 2px rgba(34,197,94,0.2);
            animation: livepulse 2s ease-in-out infinite;
        }
        @keyframes livepulse { 0%,100%{box-shadow:0 0 0 2px rgba(34,197,94,0.2)} 50%{box-shadow:0 0 0 5px rgba(34,197,94,0.08)} }

        .form-title {
            font-family: 'Syne', sans-serif;
            font-size: 1.75rem; font-weight: 800;
            color: var(--ink);
            letter-spacing: -0.025em;
            line-height: 1.1;
            margin-bottom: 8px;
        }
        .form-title .navy { color: var(--navy); }

        .form-subtitle {
            font-size: 0.875rem;
            color: var(--ink-soft);
            font-weight: 400;
            line-height: 1.5;
        }

        /* Session Status */
        .session-status {
            display: flex; align-items: center; gap: 10px;
            padding: 12px 16px;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 10px;
            font-size: 0.82rem;
            color: #15803d;
            font-weight: 500;
            margin-bottom: 20px;
        }
        .session-status svg { width: 16px; height: 16px; stroke: #16a34a; fill: none; stroke-width: 2; flex-shrink: 0; }

        /* Field group */
        .field-group { margin-bottom: 20px; }

        .field-label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--ink-mid);
            margin-bottom: 7px;
            letter-spacing: 0.02em;
        }

        .field-wrap { position: relative; }

        .field-icon {
            position: absolute;
            left: 14px; top: 50%;
            transform: translateY(-50%);
            width: 16px; height: 16px;
            stroke: var(--ink-faint);
            fill: none;
            stroke-width: 1.8;
            stroke-linecap: round;
            stroke-linejoin: round;
            pointer-events: none;
            transition: stroke 0.2s;
        }

        .field-input {
            width: 100%;
            padding: 12px 42px 12px 42px;
            background: var(--white);
            border: 1.5px solid rgba(17,31,162,0.15);
            border-radius: 11px;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.9rem;
            color: var(--ink);
            outline: none;
            transition: all 0.25s ease;
            box-shadow: 0 1px 3px rgba(17,31,162,0.04);
        }

        .field-input::placeholder { color: var(--ink-faint); }

        .field-input:hover {
            border-color: rgba(17,31,162,0.25);
            box-shadow: 0 2px 8px rgba(17,31,162,0.06);
        }

        .field-input:focus {
            border-color: var(--navy);
            box-shadow: 0 0 0 3px rgba(17,31,162,0.1), 0 2px 8px rgba(17,31,162,0.08);
        }

        .field-input:focus + .field-icon,
        .field-wrap:focus-within .field-icon { stroke: var(--navy); }

        /* Password toggle */
        .pw-toggle {
            position: absolute;
            right: 14px; top: 50%;
            transform: translateY(-50%);
            background: none; border: none;
            cursor: pointer; padding: 2px;
            color: var(--ink-faint);
            transition: color 0.2s;
            display: flex; align-items: center;
        }
        .pw-toggle:hover { color: var(--navy); }
        .pw-toggle svg { width: 16px; height: 16px; stroke: currentColor; fill: none; stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round; }

        /* Error message */
        .field-error {
            display: flex; align-items: center; gap: 5px;
            margin-top: 6px;
            font-size: 0.75rem;
            color: var(--error);
            font-weight: 500;
        }
        .field-error svg { width: 12px; height: 12px; stroke: var(--error); fill: none; stroke-width: 2; flex-shrink: 0; }

        /* Remember row */
        .remember-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 8px;
        }

        .remember-label {
            display: flex; align-items: center; gap: 8px;
            font-size: 0.82rem; color: var(--ink-mid);
            cursor: pointer;
        }

        .remember-check {
            width: 16px; height: 16px;
            border: 1.5px solid rgba(17,31,162,0.25);
            border-radius: 5px;
            background: var(--white);
            cursor: pointer;
            accent-color: var(--navy);
            transition: all 0.2s;
        }

        .forgot-link {
            font-size: 0.8rem;
            color: var(--navy);
            text-decoration: none;
            font-weight: 600;
            padding: 2px 0;
            border-bottom: 1px solid transparent;
            transition: border-color 0.2s;
        }
        .forgot-link:hover { border-bottom-color: var(--navy); }

        /* Submit button */
        .btn-submit {
            width: 100%;
            padding: 14px 24px;
            background: var(--navy);
            color: #fff;
            border: none;
            border-radius: 11px;
            font-family: 'Syne', sans-serif;
            font-size: 1rem; font-weight: 700;
            cursor: pointer;
            box-shadow: 0 6px 24px rgba(17,31,162,0.3), 0 2px 6px rgba(17,31,162,0.2);
            transition: all 0.3s ease;
            display: flex; align-items: center; justify-content: center; gap: 10px;
            position: relative; overflow: hidden;
        }

        .btn-submit::before {
            content: '';
            position: absolute; inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, transparent 60%);
        }

        .btn-submit:hover {
            background: var(--navy-light);
            transform: translateY(-2px);
            box-shadow: 0 10px 36px rgba(17,31,162,0.4), 0 4px 10px rgba(17,31,162,0.2);
        }

        .btn-submit:active { transform: translateY(0); }

        .btn-submit svg { width: 16px; height: 16px; position: relative; z-index: 1; }
        .btn-submit span { position: relative; z-index: 1; }

        /* Divider */
        .form-divider {
            display: flex; align-items: center; gap: 12px;
            margin: 24px 0;
        }
        .form-divider::before, .form-divider::after {
            content: ''; flex: 1;
            height: 1px; background: var(--border-soft);
        }
        .form-divider span {
            font-size: 0.72rem; color: var(--ink-faint);
            font-weight: 500; letter-spacing: 0.05em;
            white-space: nowrap;
        }

        /* Back to home */
        .back-home {
            display: flex; align-items: center; justify-content: center; gap: 6px;
            font-size: 0.82rem; color: var(--ink-soft);
            text-decoration: none;
            padding: 10px;
            border-radius: 9px;
            border: 1px solid var(--border-soft);
            transition: all 0.2s ease;
        }
        .back-home:hover { color: var(--navy); border-color: var(--border); background: var(--navy-xpale); }
        .back-home svg { width: 14px; height: 14px; stroke: currentColor; fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }

        /* Form footer */
        .form-footer {
            margin-top: 28px;
            padding-top: 20px;
            border-top: 1px solid var(--border-soft);
            text-align: center;
            font-size: 0.75rem;
            color: var(--ink-faint);
        }

        /* Reveal */
        .reveal { opacity: 0; transform: translateY(16px); transition: opacity 0.7s ease, transform 0.7s ease; }
        .reveal.visible { opacity: 1; transform: none; }
        .d1 { transition-delay: 0.08s; }
        .d2 { transition-delay: 0.16s; }
        .d3 { transition-delay: 0.24s; }
        .d4 { transition-delay: 0.32s; }
        .d5 { transition-delay: 0.40s; }

        /* ── RESPONSIVE ── */
        @media (max-width: 900px) {
            .panel-left { display: none; }
            .panel-right {
                width: 100%;
                border-left: none;
                padding: 40px 24px;
                min-height: 100vh;
            }
            body { background: var(--white); }
        }

        @media (max-width: 480px) {
            .panel-right { padding: 32px 20px; justify-content: flex-start; padding-top: 60px; }
            .form-title { font-size: 1.5rem; }
        }
    </style>
</head>
<body>

    <div class="bg-layer bg-gradient"></div>
    <div class="bg-layer bg-dots"></div>

    <div class="page">

        <!-- ══ LEFT PANEL ══ -->
        <div class="panel-left">
            <!-- Decorative rings -->
            <div class="lp-rings">
                <div class="lp-ring lp-ring-1">
                    <div class="lp-dot"></div>
                </div>
                <div class="lp-ring lp-ring-2"></div>
                <div class="lp-ring lp-ring-3"></div>
            </div>

            <!-- Orbs -->
            <div class="lp-orb lp-orb-1"></div>
            <div class="lp-orb lp-orb-2"></div>

            <!-- Brand -->
            <div class="lp-brand">
                <div class="lp-logo">
                    <svg viewBox="0 0 24 24">
                        <path d="M21 8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16V8z"/>
                        <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
                        <line x1="12" y1="22.08" x2="12" y2="12"/>
                    </svg>
                </div>
                <div class="lp-logo-text">
                    <div class="lp-name">HIKARI Logistik</div>
                    <div class="lp-sub">Warehouse Management</div>
                </div>
            </div>

            <!-- Center content -->
            <div class="lp-center">
                <h2 class="lp-headline">
                    Selamat Datang<br>Kembali di<br>
                    <span class="cream-word">Sistem Gudang</span><br>
                    HIKARI.
                </h2>
                <p class="lp-desc">
                    Masuk untuk mengakses dashboard, manajemen stok, dan laporan operasional gudang HIKARI secara lengkap.
                </p>
                <div class="lp-stats">
                    <div class="lp-stat">
                        <div class="lp-stat-icon">
                            <svg viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                        </div>
                        <div>
                            <div class="lp-stat-val">Dashboard Real-time</div>
                            <div class="lp-stat-lbl">Pantau seluruh gudang dalam satu layar</div>
                        </div>
                    </div>
                    <div class="lp-stat">
                        <div class="lp-stat-icon">
                            <svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                        </div>
                        <div>
                            <div class="lp-stat-val">Stok Selalu Akurat</div>
                            <div class="lp-stat-lbl">Update otomatis setiap transaksi</div>
                        </div>
                    </div>
                    <div class="lp-stat">
                        <div class="lp-stat-icon">
                            <svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                        </div>
                        <div>
                            <div class="lp-stat-val">Data Aman & Terenkripsi</div>
                            <div class="lp-stat-lbl">Keamanan tingkat enterprise</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lp-footer">&copy; 2026 HIKARI Logistik. All rights reserved.</div>
        </div>

        <!-- ══ RIGHT PANEL — FORM ══ -->
        <div class="panel-right">
            <div class="form-wrap">

                <!-- Header -->
                <div class="form-header reveal">
                    <div class="form-badge">
                        <span class="form-badge-dot"></span>
                        Sistem Aktif
                    </div>
                    <h1 class="form-title">Masuk ke <span class="navy">Akun</span><br>Anda</h1>
                    <p class="form-subtitle">Gunakan email dan password yang terdaftar di sistem HIKARI Logistik.</p>
                </div>

                <!-- Session status -->
                @if (session('status'))
                <div class="session-status reveal d1">
                    <svg viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    {{ session('status') }}
                </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email -->
                    <div class="field-group reveal d2">
                        <label class="field-label" for="email">Alamat Email</label>
                        <div class="field-wrap">
                            <svg class="field-icon" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            <input
                                id="email"
                                class="field-input {{ $errors->get('email') ? 'error' : '' }}"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                autofocus
                                autocomplete="username"
                                placeholder="email@gmail.com"
                            />
                        </div>
                        @foreach ($errors->get('email') as $message)
                        <div class="field-error">
                            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            {{ $message }}
                        </div>
                        @endforeach
                    </div>

                    <!-- Password -->
                    <div class="field-group reveal d3">
                        <label class="field-label" for="password">Password</label>
                        <div class="field-wrap">
                            <svg class="field-icon" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                            <input
                                id="password"
                                class="field-input {{ $errors->get('password') ? 'error' : '' }}"
                                type="password"
                                name="password"
                                required
                                autocomplete="current-password"
                                placeholder="••••••••"
                            />
                            <button type="button" class="pw-toggle" id="pwToggle" aria-label="Tampilkan password">
                                <svg id="eyeIcon" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                        @foreach ($errors->get('password') as $message)
                        <div class="field-error">
                            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            {{ $message }}
                        </div>
                        @endforeach
                    </div>

                    <!-- Remember + Forgot -->
                    <div class="remember-row reveal d4">
                        <label class="remember-label" for="remember_me">
                            <input id="remember_me" type="checkbox" class="remember-check" name="remember">
                            Ingat saya
                        </label>
                        @if (Route::has('password.request'))
                        <a class="forgot-link" href="{{ route('password.request') }}">Lupa password?</a>
                        @endif
                    </div>

                    <!-- Submit -->
                    <div class="reveal d4">
                        <button type="submit" class="btn-submit">
                            <span>Masuk ke Sistem</span>
                            <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 8h10M9 4l4 4-4 4"/>
                            </svg>
                        </button>
                    </div>

                </form>

                <!-- Divider + back -->
                <div class="form-divider reveal d5">
                    <span>atau</span>
                </div>

                <a href="{{ url('/') }}" class="back-home reveal d5">
                    <svg viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                    Kembali ke Halaman Utama
                </a>

                <div class="form-footer reveal d5">
                    &copy; 2026 HIKARI Logistik · Warehouse Management System
                </div>

            </div>
        </div><!-- /.panel-right -->

    </div><!-- /.page -->

    <script>
        // Reveal
        const obs = new IntersectionObserver(e => e.forEach(x => { if(x.isIntersecting){ x.target.classList.add('visible'); obs.unobserve(x.target); } }), {threshold:0.1});
        document.querySelectorAll('.reveal').forEach(el => obs.observe(el));
        setTimeout(() => document.querySelectorAll('.reveal').forEach(el => el.classList.add('visible')), 80);

        // Password toggle
        const pwInput = document.getElementById('password');
        const pwToggle = document.getElementById('pwToggle');
        const eyeIcon = document.getElementById('eyeIcon');

        const eyeOpen = `<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>`;
        const eyeClosed = `<path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>`;

        let visible = false;
        pwToggle.addEventListener('click', () => {
            visible = !visible;
            pwInput.type = visible ? 'text' : 'password';
            eyeIcon.innerHTML = visible ? eyeClosed : eyeOpen;
        });

        // Input error styling
        document.querySelectorAll('.field-input.error').forEach(el => {
            el.style.borderColor = 'var(--error)';
            el.style.boxShadow = '0 0 0 3px rgba(220,38,38,0.08)';
        });
    </script>

</body>
</html>