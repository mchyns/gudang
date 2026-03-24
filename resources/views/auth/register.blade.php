<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Akun | HIKARI Logistik</title>
    <link rel="icon" type="image/png" href="{{ asset('images/teslog.png') }}">
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

        /* ── BACKGROUND ── */
        .bg-layer { position: fixed; inset: 0; z-index: 0; pointer-events: none; }
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

        /* ── LAYOUT ── */
        .page {
            position: relative; z-index: 1;
            display: flex; width: 100%; min-height: 100vh;
        }

        /* ── LEFT PANEL ── */
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

        .panel-left::before {
            content: '';
            position: absolute; inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
            background-size: 40px 40px;
        }

        .lp-orb { position: absolute; border-radius: 50%; filter: blur(70px); pointer-events: none; }
        .lp-orb-1 { width: 380px; height: 380px; top: -80px; right: -80px; background: rgba(254,255,211,0.08); }
        .lp-orb-2 { width: 300px; height: 300px; bottom: -60px; left: -60px; background: rgba(255,255,255,0.05); }

        /* Rings */
        .lp-rings { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); }
        .lp-ring {
            position: absolute; border-radius: 50%;
            border: 1px solid rgba(255,255,255,0.06);
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
        }
        .lp-ring-1 { width: 420px; height: 420px; animation: lpspin 50s linear infinite; }
        .lp-ring-2 { width: 300px; height: 300px; border-style: dashed; border-color: rgba(254,255,211,0.07); animation: lpspin 35s linear infinite reverse; }
        .lp-ring-3 { width: 180px; height: 180px; animation: lpspin 70s linear infinite; }
        @keyframes lpspin { from{transform:translate(-50%,-50%) rotate(0)} to{transform:translate(-50%,-50%) rotate(360deg)} }

        .lp-ring-dot {
            position: absolute;
            width: 8px; height: 8px;
            background: var(--cream);
            border-radius: 50%;
            box-shadow: 0 0 14px rgba(254,255,211,0.8);
            top: -4px; left: 50%;
            transform: translateX(-50%);
        }
        .lp-ring-dot-sm {
            position: absolute;
            width: 5px; height: 5px;
            background: rgba(254,255,211,0.5);
            border-radius: 50%;
            bottom: -2.5px; right: 30%;
        }

        /* Brand */
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
        }
        .lp-logo svg { width: 20px; height: 20px; stroke: #fff; fill: none; stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round; }
        .lp-logo-name { font-family: 'Syne', sans-serif; font-size: 0.95rem; font-weight: 800; color: #fff; letter-spacing: -0.01em; }
        .lp-logo-sub { font-size: 0.67rem; color: rgba(255,255,255,0.5); letter-spacing: 0.04em; }

        /* Center content */
        .lp-center { position: relative; z-index: 1; flex: 1; display: flex; flex-direction: column; justify-content: center; }

        .lp-headline {
            font-family: 'Syne', sans-serif;
            font-size: clamp(1.8rem, 2.6vw, 2.6rem);
            font-weight: 800;
            color: #fff;
            line-height: 1.1;
            letter-spacing: -0.025em;
            margin-bottom: 16px;
        }
        .lp-headline .cream-word { color: var(--cream); }

        .lp-desc {
            font-size: 0.88rem;
            color: rgba(255,255,255,0.5);
            line-height: 1.7;
            max-width: 340px;
            font-weight: 300;
            margin-bottom: 36px;
        }

        /* Step indicator — register flow */
        .lp-steps { display: flex; flex-direction: column; gap: 0; }
        .lp-step {
            display: flex; align-items: flex-start; gap: 14px;
            padding: 16px 0;
            border-bottom: 1px solid rgba(255,255,255,0.07);
        }
        .lp-step:last-child { border-bottom: none; }

        .step-num {
            width: 32px; height: 32px; min-width: 32px;
            border-radius: 9px;
            display: flex; align-items: center; justify-content: center;
            font-family: 'Syne', sans-serif;
            font-size: 0.78rem; font-weight: 800;
        }
        .step-num.active {
            background: var(--cream);
            color: var(--navy);
            box-shadow: 0 0 16px rgba(254,255,211,0.3);
        }
        .step-num.done {
            background: rgba(34,197,94,0.2);
            border: 1px solid rgba(34,197,94,0.3);
            color: #4ade80;
        }
        .step-num.pending {
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.1);
            color: rgba(255,255,255,0.3);
        }

        .step-info { padding-top: 4px; }
        .step-title { font-size: 0.82rem; font-weight: 600; color: rgba(255,255,255,0.85); }
        .step-desc { font-size: 0.7rem; color: rgba(255,255,255,0.35); margin-top: 2px; }
        .step-num.active ~ .step-info .step-title { color: #fff; }

        /* Cream accent box */
        .lp-accent-box {
            position: relative; z-index: 1;
            background: rgba(254,255,211,0.08);
            border: 1px solid rgba(254,255,211,0.15);
            border-radius: 14px;
            padding: 16px 18px;
            margin-top: 28px;
            display: flex; align-items: center; gap: 12px;
        }
        .lp-accent-icon {
            width: 34px; height: 34px; min-width: 34px;
            background: rgba(254,255,211,0.12);
            border-radius: 9px;
            display: flex; align-items: center; justify-content: center;
        }
        .lp-accent-icon svg { width: 16px; height: 16px; stroke: var(--cream); fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
        .lp-accent-text { font-size: 0.78rem; color: rgba(255,255,255,0.6); line-height: 1.5; }
        .lp-accent-text strong { color: var(--cream); font-weight: 600; display: block; font-size: 0.82rem; margin-bottom: 1px; }

        .lp-footer { position: relative; z-index: 1; font-size: 0.72rem; color: rgba(255,255,255,0.22); }

        /* ── RIGHT PANEL ── */
        .panel-right {
            width: 520px;
            min-width: 340px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px clamp(28px, 5vw, 52px);
            background: var(--white);
            border-left: 1px solid var(--border-soft);
            position: relative;
            overflow-y: auto;
        }

        .panel-right::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--navy), var(--navy-light), transparent);
        }

        .form-wrap { width: 100%; max-width: 400px; padding: 20px 0; }

        /* Form header */
        .form-header { margin-bottom: 28px; }

        .form-badge {
            display: inline-flex; align-items: center; gap: 7px;
            font-size: 0.68rem; font-weight: 700;
            letter-spacing: 0.1em; text-transform: uppercase;
            color: var(--navy);
            background: var(--navy-xpale);
            border: 1px solid var(--border);
            border-radius: 100px;
            padding: 4px 12px;
            margin-bottom: 16px;
        }
        .form-badge-dot {
            width: 5px; height: 5px;
            background: #f59e0b;
            border-radius: 50%;
            box-shadow: 0 0 0 2px rgba(245,158,11,0.2);
            animation: regpulse 2s ease-in-out infinite;
        }
        @keyframes regpulse { 0%,100%{box-shadow:0 0 0 2px rgba(245,158,11,0.2)} 50%{box-shadow:0 0 0 5px rgba(245,158,11,0.08)} }

        .form-title {
            font-family: 'Syne', sans-serif;
            font-size: 1.7rem; font-weight: 800;
            color: var(--ink);
            letter-spacing: -0.025em;
            line-height: 1.1;
            margin-bottom: 8px;
        }
        .form-title .navy { color: var(--navy); }

        .form-subtitle { font-size: 0.85rem; color: var(--ink-soft); line-height: 1.55; font-weight: 400; }

        /* Field group */
        .field-group { margin-bottom: 16px; }

        .field-label {
            display: block;
            font-size: 0.78rem; font-weight: 600;
            color: var(--ink-mid);
            margin-bottom: 6px;
            letter-spacing: 0.02em;
        }

        .field-wrap { position: relative; }

        .field-icon {
            position: absolute;
            left: 13px; top: 50%;
            transform: translateY(-50%);
            width: 15px; height: 15px;
            stroke: var(--ink-faint);
            fill: none;
            stroke-width: 1.8;
            stroke-linecap: round; stroke-linejoin: round;
            pointer-events: none;
            transition: stroke 0.2s;
        }
        .field-wrap:focus-within .field-icon { stroke: var(--navy); }

        .field-input {
            width: 100%;
            padding: 11px 42px 11px 40px;
            background: var(--white);
            border: 1.5px solid rgba(17,31,162,0.14);
            border-radius: 10px;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.875rem;
            color: var(--ink);
            outline: none;
            transition: all 0.25s ease;
            box-shadow: 0 1px 3px rgba(17,31,162,0.04);
        }
        .field-input::placeholder { color: var(--ink-faint); font-size: 0.85rem; }
        .field-input:hover { border-color: rgba(17,31,162,0.22); box-shadow: 0 2px 8px rgba(17,31,162,0.06); }
        .field-input:focus { border-color: var(--navy); box-shadow: 0 0 0 3px rgba(17,31,162,0.1), 0 2px 8px rgba(17,31,162,0.07); }
        .field-input.has-error { border-color: var(--error); box-shadow: 0 0 0 3px rgba(220,38,38,0.08); }

        /* Password toggle */
        .pw-toggle {
            position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
            background: none; border: none; cursor: pointer; padding: 3px;
            color: var(--ink-faint); transition: color 0.2s;
            display: flex; align-items: center;
        }
        .pw-toggle:hover { color: var(--navy); }
        .pw-toggle svg { width: 15px; height: 15px; stroke: currentColor; fill: none; stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round; }

        /* Password strength */
        .pw-strength { margin-top: 8px; }
        .pw-strength-bars { display: flex; gap: 4px; margin-bottom: 4px; }
        .pw-bar {
            flex: 1; height: 3px; border-radius: 100px;
            background: rgba(17,31,162,0.08);
            transition: background 0.3s ease;
        }
        .pw-bar.weak { background: #ef4444; }
        .pw-bar.medium { background: #f59e0b; }
        .pw-bar.strong { background: #22c55e; }
        .pw-strength-label { font-size: 0.65rem; color: var(--ink-faint); font-weight: 500; }

        /* Field error */
        .field-error {
            display: flex; align-items: center; gap: 5px;
            margin-top: 5px;
            font-size: 0.73rem; color: var(--error); font-weight: 500;
        }
        .field-error svg { width: 11px; height: 11px; stroke: var(--error); fill: none; stroke-width: 2.2; flex-shrink: 0; }

        /* Row grid — 2 cols */
        .field-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }

        /* Divider */
        .section-divider {
            display: flex; align-items: center; gap: 10px;
            margin: 8px 0 16px;
        }
        .section-divider::before, .section-divider::after { content: ''; flex: 1; height: 1px; background: var(--border-soft); }
        .section-divider span { font-size: 0.68rem; color: var(--ink-faint); font-weight: 600; letter-spacing: 0.06em; text-transform: uppercase; white-space: nowrap; }

        /* Submit */
        .btn-submit {
            width: 100%;
            padding: 14px 24px;
            background: var(--navy);
            color: #fff;
            border: none; border-radius: 11px;
            font-family: 'Syne', sans-serif;
            font-size: 0.975rem; font-weight: 700;
            cursor: pointer;
            box-shadow: 0 6px 24px rgba(17,31,162,0.3), 0 2px 6px rgba(17,31,162,0.15);
            transition: all 0.3s ease;
            display: flex; align-items: center; justify-content: center; gap: 10px;
            position: relative; overflow: hidden;
            margin-top: 20px;
        }
        .btn-submit::before {
            content: ''; position: absolute; inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, transparent 60%);
        }
        .btn-submit:hover { background: var(--navy-light); transform: translateY(-2px); box-shadow: 0 10px 36px rgba(17,31,162,0.4); }
        .btn-submit:active { transform: translateY(0); }
        .btn-submit svg { width: 15px; height: 15px; position: relative; z-index: 1; }
        .btn-submit span { position: relative; z-index: 1; }

        /* Terms note */
        .terms-note {
            margin-top: 12px;
            font-size: 0.72rem;
            color: var(--ink-faint);
            text-align: center;
            line-height: 1.6;
        }
        .terms-note a { color: var(--navy); text-decoration: none; font-weight: 500; }
        .terms-note a:hover { text-decoration: underline; }

        /* Login link */
        .login-link-wrap {
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid var(--border-soft);
            text-align: center;
        }
        .login-link-wrap p { font-size: 0.82rem; color: var(--ink-soft); }
        .login-link {
            color: var(--navy);
            text-decoration: none;
            font-weight: 700;
            font-family: 'Syne', sans-serif;
        }
        .login-link:hover { text-decoration: underline; }

        /* Back home */
        .back-home {
            display: inline-flex; align-items: center; gap: 5px;
            font-size: 0.75rem; color: var(--ink-faint);
            text-decoration: none; margin-top: 12px;
            transition: color 0.2s;
        }
        .back-home:hover { color: var(--navy); }
        .back-home svg { width: 13px; height: 13px; stroke: currentColor; fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }

        /* Reveals */
        .reveal { opacity: 0; transform: translateY(16px); transition: opacity 0.7s ease, transform 0.7s ease; }
        .reveal.visible { opacity: 1; transform: none; }
        .d1 { transition-delay: 0.06s; }
        .d2 { transition-delay: 0.12s; }
        .d3 { transition-delay: 0.18s; }
        .d4 { transition-delay: 0.24s; }
        .d5 { transition-delay: 0.30s; }
        .d6 { transition-delay: 0.36s; }

        /* ── RESPONSIVE ── */
        @media (max-width: 960px) {
            .panel-left { display: none; }
            .panel-right { width: 100%; border-left: none; min-height: 100vh; padding: 40px 24px; }
            body { background: var(--white); }
        }
        @media (max-width: 520px) {
            .field-row { grid-template-columns: 1fr; }
            .form-title { font-size: 1.45rem; }
            .panel-right { padding: 36px 18px; }
        }
    </style>
</head>
<body>

    <div class="bg-layer bg-gradient"></div>
    <div class="bg-layer bg-dots"></div>

    <div class="page">

        <!-- ══ LEFT PANEL ══ -->
        <div class="panel-left">
            <div class="lp-rings">
                <div class="lp-ring lp-ring-1">
                    <div class="lp-ring-dot"></div>
                    <div class="lp-ring-dot-sm"></div>
                </div>
                <div class="lp-ring lp-ring-2"></div>
                <div class="lp-ring lp-ring-3"></div>
            </div>
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
                <div>
                    <div class="lp-logo-name">HIKARI Logistik</div>
                    <div class="lp-logo-sub">Warehouse Management System</div>
                </div>
            </div>

            <!-- Center -->
            <div class="lp-center">
                <h2 class="lp-headline">
                    Bergabung &<br>Kelola Gudang<br>
                    <span class="cream-word">HIKARI</span><br>
                    Bersama Kami.
                </h2>
                <p class="lp-desc">
                    Buat akun untuk mengakses seluruh fitur sistem manajemen gudang HIKARI Logistik — stok, transaksi, dan laporan dalam satu platform.
                </p>

                <!-- Steps -->
                <div class="lp-steps">
                    <div class="lp-step">
                        <div class="step-num active">01</div>
                        <div class="step-info">
                            <div class="step-title">Isi Data Akun</div>
                            <div class="step-desc">Nama, email, dan password Anda</div>
                        </div>
                    </div>
                    <div class="lp-step">
                        <div class="step-num pending">02</div>
                        <div class="step-info">
                            <div class="step-title">Verifikasi Admin</div>
                            <div class="step-desc">Admin menyetujui akses Anda</div>
                        </div>
                    </div>
                    <div class="lp-step">
                        <div class="step-num pending">03</div>
                        <div class="step-info">
                            <div class="step-title">Akses Penuh</div>
                            <div class="step-desc">Dashboard & fitur siap digunakan</div>
                        </div>
                    </div>
                </div>

                <div class="lp-accent-box">
                    <div class="lp-accent-icon">
                        <svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </div>
                    <div class="lp-accent-text">
                        <strong>Data Aman & Terproteksi</strong>
                        Informasi akun Anda dienkripsi dan tidak akan dibagikan kepada pihak ketiga.
                    </div>
                </div>
            </div>

            <div class="lp-footer">&copy; 2026 HIKARI Logistik. All rights reserved.</div>
        </div>

        <!-- ══ RIGHT PANEL ══ -->
        <div class="panel-right">
            <div class="form-wrap">

                <!-- Header -->
                <div class="form-header reveal">
                    <div class="form-badge">
                        <span class="form-badge-dot"></span>
                        Pendaftaran Akun Baru
                    </div>
                    <h1 class="form-title">Buat Akun <span class="navy">HIKARI</span><br>Anda Sekarang</h1>
                    <p class="form-subtitle">Lengkapi formulir berikut untuk mendaftar ke sistem gudang HIKARI Logistik.</p>
                </div>

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <!-- Name -->
                    <div class="field-group reveal d1">
                        <label class="field-label" for="name">Nama Lengkap</label>
                        <div class="field-wrap">
                            <svg class="field-icon" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            <input
                                id="name"
                                class="field-input {{ $errors->get('name') ? 'has-error' : '' }}"
                                type="text"
                                name="name"
                                value="{{ old('name') }}"
                                required autofocus autocomplete="name"
                                placeholder="Nama lengkap Anda"
                            />
                        </div>
                        @foreach ($errors->get('name') as $message)
                        <div class="field-error">
                            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            {{ $message }}
                        </div>
                        @endforeach
                    </div>

                    <!-- Email -->
                    <div class="field-group reveal d2">
                        <label class="field-label" for="email">Alamat Email</label>
                        <div class="field-wrap">
                            <svg class="field-icon" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            <input
                                id="email"
                                class="field-input {{ $errors->get('email') ? 'has-error' : '' }}"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required autocomplete="username"
                                placeholder="email@hikari.co.id"
                            />
                        </div>
                        @foreach ($errors->get('email') as $message)
                        <div class="field-error">
                            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            {{ $message }}
                        </div>
                        @endforeach
                    </div>

                    <!-- Passwords in 2-col grid on large screens -->
                    <div class="section-divider reveal d3"><span>Keamanan Akun</span></div>

                    <div class="field-group reveal d3">
                        <label class="field-label" for="password">Password</label>
                        <div class="field-wrap">
                            <svg class="field-icon" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                            <input
                                id="password"
                                class="field-input {{ $errors->get('password') ? 'has-error' : '' }}"
                                type="password"
                                name="password"
                                required autocomplete="new-password"
                                placeholder="Minimal 8 karakter"
                            />
                            <button type="button" class="pw-toggle" id="pwToggle1" aria-label="Tampilkan password">
                                <svg id="eye1" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                        <!-- Password strength -->
                        <div class="pw-strength" id="pwStrengthWrap" style="display:none">
                            <div class="pw-strength-bars">
                                <div class="pw-bar" id="bar1"></div>
                                <div class="pw-bar" id="bar2"></div>
                                <div class="pw-bar" id="bar3"></div>
                                <div class="pw-bar" id="bar4"></div>
                            </div>
                            <div class="pw-strength-label" id="pwStrengthLabel">Masukkan password</div>
                        </div>
                        @foreach ($errors->get('password') as $message)
                        <div class="field-error">
                            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            {{ $message }}
                        </div>
                        @endforeach
                    </div>

                    <!-- Confirm Password -->
                    <div class="field-group reveal d4">
                        <label class="field-label" for="password_confirmation">Konfirmasi Password</label>
                        <div class="field-wrap">
                            <svg class="field-icon" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            <input
                                id="password_confirmation"
                                class="field-input {{ $errors->get('password_confirmation') ? 'has-error' : '' }}"
                                type="password"
                                name="password_confirmation"
                                required autocomplete="new-password"
                                placeholder="Ulangi password Anda"
                            />
                            <button type="button" class="pw-toggle" id="pwToggle2" aria-label="Tampilkan konfirmasi password">
                                <svg id="eye2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                        @foreach ($errors->get('password_confirmation') as $message)
                        <div class="field-error">
                            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            {{ $message }}
                        </div>
                        @endforeach
                    </div>

                    <!-- Submit -->
                    <div class="reveal d5">
                        <button type="submit" class="btn-submit">
                            <span>Buat Akun Sekarang</span>
                            <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 8h10M9 4l4 4-4 4"/>
                            </svg>
                        </button>
                    </div>

                    <p class="terms-note reveal d5">
                        Dengan mendaftar, Anda menyetujui <a href="#">Ketentuan Penggunaan</a> dan <a href="#">Kebijakan Privasi</a> HIKARI Logistik.
                    </p>

                </form>

                <!-- Login link -->
                <div class="login-link-wrap reveal d6">
                    <p>Sudah punya akun? <a class="login-link" href="{{ route('login') }}">Masuk di sini</a></p>
                    <a href="{{ url('/') }}" class="back-home">
                        <svg viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                        Kembali ke halaman utama
                    </a>
                </div>

            </div>
        </div>

    </div><!-- /.page -->

    <script>
        // Reveals
        const obs = new IntersectionObserver(e => e.forEach(x => {
            if (x.isIntersecting) { x.target.classList.add('visible'); obs.unobserve(x.target); }
        }), { threshold: 0.08 });
        document.querySelectorAll('.reveal').forEach(el => obs.observe(el));
        setTimeout(() => document.querySelectorAll('.reveal').forEach(el => el.classList.add('visible')), 80);

        // Password toggle factory
        function makePwToggle(inputId, toggleId, eyeId) {
            const input = document.getElementById(inputId);
            const btn = document.getElementById(toggleId);
            const icon = document.getElementById(eyeId);
            const open  = `<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>`;
            const closed = `<path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>`;
            let vis = false;
            btn.addEventListener('click', () => {
                vis = !vis;
                input.type = vis ? 'text' : 'password';
                icon.innerHTML = vis ? closed : open;
            });
        }
        makePwToggle('password', 'pwToggle1', 'eye1');
        makePwToggle('password_confirmation', 'pwToggle2', 'eye2');

        // Password strength meter
        const pwInput = document.getElementById('password');
        const strengthWrap = document.getElementById('pwStrengthWrap');
        const bars = [document.getElementById('bar1'), document.getElementById('bar2'), document.getElementById('bar3'), document.getElementById('bar4')];
        const label = document.getElementById('pwStrengthLabel');

        function calcStrength(pw) {
            let score = 0;
            if (pw.length >= 8) score++;
            if (pw.length >= 12) score++;
            if (/[A-Z]/.test(pw) && /[a-z]/.test(pw)) score++;
            if (/[0-9]/.test(pw) && /[^A-Za-z0-9]/.test(pw)) score++;
            return score;
        }

        const levels = [
            { cls: 'weak',   text: 'Terlalu lemah',  color: '#ef4444' },
            { cls: 'weak',   text: 'Lemah',           color: '#ef4444' },
            { cls: 'medium', text: 'Cukup kuat',      color: '#f59e0b' },
            { cls: 'medium', text: 'Kuat',             color: '#f59e0b' },
            { cls: 'strong', text: 'Sangat kuat',      color: '#22c55e' },
        ];

        pwInput.addEventListener('input', () => {
            const val = pwInput.value;
            if (!val) { strengthWrap.style.display = 'none'; return; }
            strengthWrap.style.display = 'block';
            const score = calcStrength(val);
            const lvl = levels[score];
            bars.forEach((bar, i) => {
                bar.className = 'pw-bar';
                if (i < score + 1) bar.classList.add(lvl.cls);
            });
            label.textContent = lvl.text;
            label.style.color = lvl.color;
        });
    </script>

</body>
</html>