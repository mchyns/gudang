<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem Gudang Logistik | MBG</title>
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
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: #f8f9ff;
            color: var(--ink);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ── BACKGROUND ── */
        .bg-layer {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
        }

        .bg-gradient {
            background:
                radial-gradient(ellipse 70% 55% at 100% 0%, rgba(17,31,162,0.07) 0%, transparent 65%),
                radial-gradient(ellipse 50% 45% at 0% 100%, rgba(17,31,162,0.05) 0%, transparent 60%),
                radial-gradient(ellipse 80% 60% at 50% 50%, rgba(254,255,211,0.4) 0%, transparent 80%),
                #f8f9ff;
        }

        .bg-dots {
            background-image: radial-gradient(circle, rgba(17,31,162,0.08) 1px, transparent 1px);
            background-size: 28px 28px;
            opacity: 0.6;
        }

        /* ── NAVBAR ── */
        nav {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 100;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 clamp(1.25rem, 5vw, 4rem);
            background: rgba(248,249,255,0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border-soft);
            transition: all 0.3s ease;
        }

        nav.scrolled {
            background: rgba(255,255,255,0.95);
            border-bottom-color: var(--border);
            box-shadow: 0 4px 24px rgba(17,31,162,0.06);
        }

        .nav-logo { display: flex; align-items: center; gap: 11px; text-decoration: none; }

        .logo-mark {
            width: 40px; height: 40px;
            background: var(--navy);
            border-radius: 11px;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 16px rgba(17,31,162,0.3);
            position: relative;
            overflow: hidden;
            flex-shrink: 0;
        }

        .logo-mark::after {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 50%;
            background: linear-gradient(to bottom, rgba(255,255,255,0.18), transparent);
            border-radius: 11px 11px 0 0;
        }

        .logo-mark svg { width: 20px; height: 20px; fill: none; stroke: #fff; stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round; position: relative; z-index: 1; }

        .logo-text { line-height: 1.1; }
        .logo-name {
            font-family: 'Syne', sans-serif;
            font-size: 0.95rem;
            font-weight: 800;
            color: var(--ink);
            letter-spacing: -0.01em;
        }
        .logo-name em { font-style: normal; color: var(--navy); }
        .logo-sub { font-size: 0.68rem; color: var(--ink-soft); font-weight: 400; letter-spacing: 0.04em; }

        .nav-right { display: flex; align-items: center; gap: 8px; }

        .btn-nav-ghost {
            font-size: 0.875rem; font-weight: 500;
            color: var(--ink-mid);
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 8px;
            border: 1px solid transparent;
            transition: all 0.2s ease;
            background: transparent;
        }
        .btn-nav-ghost:hover { color: var(--navy); background: var(--navy-xpale); border-color: var(--border); }

        .btn-nav-solid {
            font-size: 0.875rem; font-weight: 600;
            color: #fff;
            text-decoration: none;
            padding: 9px 22px;
            border-radius: 9px;
            background: var(--navy);
            border: 1px solid var(--navy);
            box-shadow: 0 4px 16px rgba(17,31,162,0.25);
            transition: all 0.25s ease;
        }
        .btn-nav-solid:hover { background: var(--navy-light); box-shadow: 0 6px 24px rgba(17,31,162,0.35); transform: translateY(-1px); }

        /* ── WRAPPER ── */
        .page { position: relative; z-index: 1; }

        /* ── HERO ── */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 90px clamp(1.25rem, 5vw, 4rem) 80px;
            max-width: 1360px;
            margin: 0 auto;
        }

        .hero-inner {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 72px;
            align-items: center;
            width: 100%;
        }

        /* Badge */
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 5px 14px 5px 6px;
            background: var(--navy-xpale);
            border: 1px solid var(--border);
            border-radius: 100px;
            font-size: 0.72rem;
            font-weight: 600;
            color: var(--navy);
            letter-spacing: 0.06em;
            text-transform: uppercase;
            margin-bottom: 28px;
        }

        .badge-pill {
            background: var(--navy);
            color: #fff;
            font-size: 0.62rem;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 100px;
            letter-spacing: 0.04em;
        }

        .live-dot {
            width: 7px; height: 7px;
            background: #22c55e;
            border-radius: 50%;
            box-shadow: 0 0 0 2px rgba(34,197,94,0.25);
            animation: livepulse 2s ease-in-out infinite;
            margin-left: 4px;
        }
        @keyframes livepulse { 0%,100%{box-shadow:0 0 0 2px rgba(34,197,94,0.25)} 50%{box-shadow:0 0 0 5px rgba(34,197,94,0.1)} }

        /* Headline */
        .hero-h1 {
            font-family: 'Syne', sans-serif;
            font-size: clamp(2.8rem, 5vw, 4.8rem);
            font-weight: 800;
            line-height: 1.03;
            letter-spacing: -0.03em;
            color: var(--ink);
            margin-bottom: 22px;
        }

        .hero-h1 .line-blue {
            color: var(--navy);
            position: relative;
            display: inline-block;
        }

        .hero-h1 .line-blue::after {
            content: '';
            position: absolute;
            bottom: -4px; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--navy), var(--navy-light), transparent);
            border-radius: 2px;
        }

        .hero-h1 .line-cream-bg {
            background: linear-gradient(135deg, var(--navy) 0%, var(--navy-light) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-sub {
            font-size: clamp(0.95rem, 1.5vw, 1.1rem);
            color: var(--ink-soft);
            line-height: 1.75;
            max-width: 460px;
            margin-bottom: 40px;
            font-weight: 400;
        }

        /* CTA */
        .cta-row { display: flex; flex-wrap: wrap; gap: 12px; align-items: center; }

        .btn-hero-primary {
            display: inline-flex; align-items: center; gap: 10px;
            padding: 15px 30px;
            background: var(--navy);
            color: #fff;
            font-family: 'Syne', sans-serif;
            font-size: 0.95rem; font-weight: 700;
            border-radius: 12px;
            text-decoration: none;
            border: none; cursor: pointer;
            box-shadow: 0 6px 24px rgba(17,31,162,0.3), 0 2px 6px rgba(17,31,162,0.2);
            transition: all 0.3s ease;
            position: relative; overflow: hidden;
        }
        .btn-hero-primary::before {
            content: '';
            position: absolute; inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.12) 0%, transparent 60%);
        }
        .btn-hero-primary:hover { background: var(--navy-light); transform: translateY(-2px); box-shadow: 0 10px 36px rgba(17,31,162,0.38), 0 4px 10px rgba(17,31,162,0.2); }
        .btn-hero-primary svg { width: 16px; height: 16px; position: relative; z-index: 1; }
        .btn-hero-primary span { position: relative; z-index: 1; }

        .btn-hero-secondary {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 15px 24px;
            background: var(--white);
            color: var(--ink-mid);
            font-size: 0.9rem; font-weight: 500;
            border-radius: 12px;
            text-decoration: none;
            border: 1px solid var(--border);
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(17,31,162,0.05);
            transition: all 0.25s ease;
        }
        .btn-hero-secondary:hover { color: var(--navy); border-color: var(--navy); background: var(--navy-xpale); box-shadow: 0 4px 16px rgba(17,31,162,0.1); }
        .btn-hero-secondary svg { width: 15px; height: 15px; }

        /* Stats */
        .stats-strip {
            display: flex; gap: 0;
            margin-top: 52px;
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(17,31,162,0.06);
            max-width: 460px;
        }

        .stat-block {
            flex: 1;
            padding: 18px 20px;
            border-right: 1px solid var(--border-soft);
            text-align: center;
        }
        .stat-block:last-child { border-right: none; }
        .stat-num { font-family: 'Syne', sans-serif; font-size: 1.5rem; font-weight: 800; color: var(--navy); line-height: 1; }
        .stat-lbl { font-size: 0.68rem; color: var(--ink-faint); margin-top: 4px; font-weight: 500; letter-spacing: 0.04em; text-transform: uppercase; }

        /* ── HERO VISUAL ── */
        .hero-visual { position: relative; }

        .visual-card-main {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 28px;
            padding: 28px;
            box-shadow: 0 20px 60px rgba(17,31,162,0.1), 0 4px 16px rgba(17,31,162,0.06);
            position: relative;
            overflow: hidden;
        }

        .visual-card-main::before {
            content: '';
            position: absolute;
            top: -60px; right: -60px;
            width: 220px; height: 220px;
            background: radial-gradient(circle, rgba(17,31,162,0.08) 0%, transparent 70%);
        }

        .visual-card-main::after {
            content: '';
            position: absolute;
            bottom: -40px; left: -40px;
            width: 180px; height: 180px;
            background: radial-gradient(circle, rgba(254,255,211,0.6) 0%, transparent 70%);
        }

        /* Top bar of card */
        .vc-topbar {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 22px;
            position: relative; z-index: 1;
        }
        .vc-topbar-title { font-family: 'Syne', sans-serif; font-size: 0.9rem; font-weight: 700; color: var(--ink); }
        .vc-topbar-meta { font-size: 0.7rem; color: var(--ink-soft); }
        .vc-status { display: flex; align-items: center; gap: 5px; font-size: 0.7rem; color: #16a34a; font-weight: 600; background: #f0fdf4; border: 1px solid #bbf7d0; padding: 3px 10px; border-radius: 100px; }
        .vc-status-dot { width: 5px; height: 5px; background: #22c55e; border-radius: 50%; }

        /* Metric row */
        .vc-metrics { display: grid; grid-template-columns: repeat(3,1fr); gap: 10px; margin-bottom: 18px; position: relative; z-index: 1; }
        .vc-metric {
            background: var(--navy-xpale);
            border: 1px solid var(--border-soft);
            border-radius: 14px;
            padding: 14px 12px;
            text-align: center;
        }
        .vc-metric-val { font-family: 'Syne', sans-serif; font-size: 1.2rem; font-weight: 800; color: var(--navy); }
        .vc-metric-lbl { font-size: 0.63rem; color: var(--ink-soft); margin-top: 3px; font-weight: 500; letter-spacing: 0.03em; }
        .vc-metric-chg { font-size: 0.65rem; color: #16a34a; font-weight: 600; margin-top: 2px; }

        /* Progress list */
        .vc-progress-list { display: flex; flex-direction: column; gap: 10px; position: relative; z-index: 1; }
        .vc-progress-item { }
        .vc-pi-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; }
        .vc-pi-name { font-size: 0.75rem; font-weight: 500; color: var(--ink-mid); }
        .vc-pi-val { font-size: 0.72rem; font-weight: 600; color: var(--navy); }
        .vc-pi-track { height: 5px; background: var(--navy-xpale); border-radius: 100px; overflow: hidden; }
        .vc-pi-fill { height: 100%; border-radius: 100px; background: linear-gradient(90deg, var(--navy), var(--navy-light)); }

        /* Floating chips */
        .float-chip {
            position: absolute;
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 12px 16px;
            box-shadow: 0 8px 28px rgba(17,31,162,0.1), 0 2px 8px rgba(0,0,0,0.04);
        }
        .fc-a { top: -28px; right: -20px; animation: floatchip 5s ease-in-out infinite; }
        .fc-b { bottom: 60px; left: -32px; animation: floatchip 7s ease-in-out infinite 1.2s; }
        .fc-c { bottom: -20px; right: 40px; animation: floatchip 6s ease-in-out infinite 0.6s; }

        @keyframes floatchip { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-10px)} }

        .chip-label { font-size: 0.62rem; color: var(--ink-soft); text-transform: uppercase; letter-spacing: 0.07em; margin-bottom: 5px; font-weight: 600; }
        .chip-val { font-family: 'Syne', sans-serif; font-size: 1.05rem; font-weight: 800; color: var(--ink); white-space: nowrap; }
        .chip-tag { display: inline-block; margin-top: 4px; font-size: 0.62rem; font-weight: 600; padding: 2px 8px; border-radius: 100px; }
        .chip-green { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
        .chip-blue { background: var(--navy-xpale); color: var(--navy); border: 1px solid var(--border); }
        .chip-yellow { background: #fefce8; color: #a16207; border: 1px solid #fde68a; }

        /* ─────────────────────────────────
           SECTION: FITUR
        ───────────────────────────────── */
        .section-wrap {
            max-width: 1360px;
            margin: 0 auto;
            padding: clamp(72px,10vw,120px) clamp(1.25rem,5vw,4rem);
        }

        .section-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--navy);
            margin-bottom: 16px;
        }
        .section-eyebrow::before {
            content: '';
            display: block; width: 20px; height: 2px;
            background: var(--navy);
            border-radius: 2px;
        }

        .section-h2 {
            font-family: 'Syne', sans-serif;
            font-size: clamp(2rem, 4vw, 3rem);
            font-weight: 800;
            color: var(--ink);
            line-height: 1.08;
            letter-spacing: -0.025em;
        }

        .section-lead {
            font-size: 1rem;
            color: var(--ink-soft);
            line-height: 1.7;
            max-width: 500px;
            margin-top: 14px;
            font-weight: 400;
        }

        /* Features grid */
        .feat-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-top: 56px;
        }

        .feat-card {
            background: var(--white);
            border: 1px solid var(--border-soft);
            border-radius: 20px;
            padding: 28px 26px;
            cursor: default;
            position: relative;
            overflow: hidden;
            transition: all 0.35s ease;
        }
        .feat-card::after {
            content: '';
            position: absolute;
            bottom: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--navy), var(--navy-light));
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.35s ease;
        }
        .feat-card:hover { border-color: var(--border); box-shadow: 0 12px 40px rgba(17,31,162,0.1); transform: translateY(-4px); }
        .feat-card:hover::after { transform: scaleX(1); }

        .feat-card.highlight {
            background: var(--navy);
            border-color: var(--navy);
        }
        .feat-card.highlight::after { display: none; }
        .feat-card.highlight .feat-icon { background: rgba(255,255,255,0.12); border-color: rgba(255,255,255,0.15); }
        .feat-card.highlight .feat-icon svg { stroke: #fff; }
        .feat-card.highlight .feat-title { color: #fff; }
        .feat-card.highlight .feat-desc { color: rgba(255,255,255,0.65); }
        .feat-card.highlight .feat-tag { color: rgba(255,255,255,0.35); border-color: rgba(255,255,255,0.12); }
        .feat-card.highlight:hover { box-shadow: 0 16px 48px rgba(17,31,162,0.35); }

        .feat-icon {
            width: 46px; height: 46px;
            background: var(--navy-xpale);
            border: 1px solid var(--border);
            border-radius: 13px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 20px;
            transition: all 0.3s;
        }
        .feat-card:not(.highlight):hover .feat-icon { background: var(--navy-xpale); border-color: var(--navy); box-shadow: 0 0 0 4px var(--navy-xpale); }
        .feat-icon svg { width: 20px; height: 20px; stroke: var(--navy); fill: none; stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round; }

        .feat-title { font-family: 'Syne', sans-serif; font-size: 1rem; font-weight: 700; color: var(--ink); margin-bottom: 8px; }
        .feat-desc { font-size: 0.83rem; color: var(--ink-soft); line-height: 1.65; font-weight: 400; }
        .feat-tag {
            display: inline-block;
            margin-top: 16px;
            font-size: 0.65rem; font-weight: 600;
            color: var(--navy);
            border: 1px solid var(--border);
            border-radius: 100px;
            padding: 3px 10px;
            letter-spacing: 0.04em;
            background: var(--navy-xpale);
        }

        /* ─────────────────────────────────
           SECTION: PREVIEW
        ───────────────────────────────── */
        .preview-inner {
            display: grid;
            grid-template-columns: 1fr 1.3fr;
            gap: 64px;
            align-items: center;
            background: var(--white);
            border: 1px solid var(--border-soft);
            border-radius: 28px;
            padding: clamp(36px, 5vw, 64px);
            box-shadow: 0 8px 40px rgba(17,31,162,0.07);
            position: relative;
            overflow: hidden;
        }

        .preview-inner::before {
            content: '';
            position: absolute;
            top: -80px; right: -80px;
            width: 300px; height: 300px;
            background: radial-gradient(circle, rgba(254,255,211,0.7) 0%, transparent 70%);
        }

        .preview-text { position: relative; z-index: 1; }

        .checklist { list-style: none; margin-top: 28px; display: flex; flex-direction: column; gap: 12px; }
        .checklist li {
            display: flex; align-items: center; gap: 10px;
            font-size: 0.88rem; color: var(--ink-mid); font-weight: 400;
        }
        .check-icon {
            width: 22px; height: 22px; min-width: 22px;
            background: var(--navy-xpale);
            border: 1px solid var(--border);
            border-radius: 7px;
            display: flex; align-items: center; justify-content: center;
        }
        .check-icon svg { width: 11px; height: 11px; stroke: var(--navy); stroke-width: 2.5; fill: none; }

        /* Dashboard mockup */
        .dash-mockup {
            background: #f8f9ff;
            border: 1px solid var(--border);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(17,31,162,0.1);
            position: relative; z-index: 1;
        }

        .dash-titlebar {
            background: var(--navy);
            padding: 11px 18px;
            display: flex; align-items: center; gap: 8px;
        }
        .dtb-dot { width: 9px; height: 9px; border-radius: 50%; }
        .dtb-r { background: rgba(255,255,255,0.3); }
        .dtb-y { background: rgba(255,255,255,0.2); }
        .dtb-g { background: rgba(255,255,255,0.15); }
        .dtb-title { font-size: 0.68rem; color: rgba(255,255,255,0.6); margin: 0 auto; letter-spacing: 0.08em; font-weight: 500; }

        .dash-body { padding: 16px; display: flex; flex-direction: column; gap: 12px; }

        .dash-kpi { display: grid; grid-template-columns: repeat(3,1fr); gap: 8px; }
        .kpi-box {
            background: var(--white);
            border: 1px solid var(--border-soft);
            border-radius: 12px;
            padding: 12px 10px;
            text-align: center;
        }
        .kpi-val { font-family: 'Syne', sans-serif; font-size: 1.1rem; font-weight: 800; color: var(--navy); }
        .kpi-lbl { font-size: 0.58rem; color: var(--ink-soft); text-transform: uppercase; letter-spacing: 0.06em; margin-top: 3px; }
        .kpi-chg { font-size: 0.6rem; color: #16a34a; font-weight: 600; margin-top: 2px; }

        .dash-table {
            background: var(--white);
            border: 1px solid var(--border-soft);
            border-radius: 12px;
            overflow: hidden;
        }
        .dt-row { display: grid; grid-template-columns: 1.8fr 0.8fr 1fr; padding: 8px 14px; border-bottom: 1px solid var(--border-soft); align-items: center; }
        .dt-row:last-child { border: none; }
        .dt-row.hd { background: var(--navy-xpale); }
        .dt-cell { font-size: 0.62rem; }
        .dt-row.hd .dt-cell { color: var(--navy); font-weight: 700; letter-spacing: 0.04em; text-transform: uppercase; }
        .dt-row:not(.hd) .dt-cell { color: var(--ink-soft); }
        .dt-row:not(.hd) .dt-cell:first-child { color: var(--ink); font-weight: 500; }

        .status-tag { font-size: 0.58rem; padding: 2px 7px; border-radius: 100px; font-weight: 600; display: inline-block; }
        .st-in { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
        .st-out { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }

        .dash-bars {
            background: var(--white);
            border: 1px solid var(--border-soft);
            border-radius: 12px;
            padding: 12px 14px;
        }
        .db-title { font-size: 0.62rem; color: var(--ink-soft); font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 10px; }
        .db-chart { display: flex; align-items: flex-end; gap: 5px; height: 44px; }
        .db-bar { flex: 1; border-radius: 4px 4px 0 0; background: var(--navy-xpale); transition: background 0.2s; }
        .db-bar.active { background: var(--navy); }
        .db-bar.accent { background: var(--cream-dark); }

        /* ─────────────────────────────────
           SECTION: CTA
        ───────────────────────────────── */
        .cta-wrapper {
            max-width: 1360px;
            margin: 0 auto;
            padding: 0 clamp(1.25rem,5vw,4rem) clamp(72px,10vw,120px);
        }

        .cta-box {
            background: var(--navy);
            border-radius: 28px;
            padding: clamp(52px,8vw,88px) clamp(36px,6vw,72px);
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 40px;
            align-items: center;
            position: relative;
            overflow: hidden;
            box-shadow: 0 24px 80px rgba(17,31,162,0.35);
        }

        .cta-box::before {
            content: '';
            position: absolute;
            top: -100px; right: 20%;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(255,255,255,0.06) 0%, transparent 70%);
        }
        .cta-box::after {
            content: '';
            position: absolute;
            bottom: -60px; left: -60px;
            width: 280px; height: 280px;
            background: radial-gradient(circle, rgba(254,255,211,0.08) 0%, transparent 70%);
        }

        .cta-box > * { position: relative; z-index: 1; }

        /* Cream accent stripe top */
        .cta-box-top {
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--cream) 0%, transparent 60%);
        }

        .cta-text .cta-eyebrow {
            font-size: 0.7rem; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase;
            color: rgba(254,255,211,0.6); margin-bottom: 12px;
            display: flex; align-items: center; gap: 8px;
        }
        .cta-text .cta-eyebrow::before { content:''; display:block; width:16px; height:1px; background:rgba(254,255,211,0.4); }

        .cta-h2 {
            font-family: 'Syne', sans-serif;
            font-size: clamp(1.8rem, 3.5vw, 2.8rem);
            font-weight: 800;
            color: #fff;
            line-height: 1.1;
            letter-spacing: -0.02em;
            margin-bottom: 12px;
        }

        .cta-h2 .cream { color: var(--cream); }

        .cta-desc { font-size: 0.95rem; color: rgba(255,255,255,0.55); line-height: 1.65; font-weight: 300; max-width: 480px; }

        .cta-actions { display: flex; flex-direction: column; gap: 10px; align-items: flex-start; flex-shrink: 0; }

        .btn-cta-cream {
            display: inline-flex; align-items: center; gap: 10px;
            padding: 14px 28px;
            background: var(--cream);
            color: var(--navy);
            font-family: 'Syne', sans-serif;
            font-size: 0.95rem; font-weight: 700;
            border-radius: 11px;
            text-decoration: none;
            border: 1px solid var(--cream-dark);
            box-shadow: 0 4px 20px rgba(254,255,211,0.2);
            white-space: nowrap;
            transition: all 0.25s ease;
        }
        .btn-cta-cream:hover { background: #fff; transform: translateY(-2px); box-shadow: 0 8px 28px rgba(254,255,211,0.3); }
        .btn-cta-cream svg { width: 15px; height: 15px; }

        .btn-cta-ghost {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 12px 20px;
            background: transparent;
            color: rgba(255,255,255,0.65);
            font-size: 0.875rem; font-weight: 500;
            border-radius: 10px;
            text-decoration: none;
            border: 1px solid rgba(255,255,255,0.15);
            white-space: nowrap;
            transition: all 0.25s ease;
            cursor: pointer;
        }
        .btn-cta-ghost:hover { color: #fff; border-color: rgba(255,255,255,0.3); background: rgba(255,255,255,0.06); }

        /* ─────────────────────────────────
           SECTION: CREAM ACCENT BAND
        ───────────────────────────────── */
        .cream-band {
            max-width: 1360px;
            margin: 0 auto;
            padding: 0 clamp(1.25rem,5vw,4rem);
        }

        .cream-strip {
            background: var(--cream);
            border-radius: 20px;
            padding: 28px 36px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
            border: 1px solid var(--cream-dark);
            margin-bottom: 60px;
            box-shadow: 0 4px 24px rgba(254,255,211,0.4);
        }

        .cream-strip-text { font-family: 'Syne', sans-serif; font-size: 1rem; font-weight: 700; color: var(--ink); }
        .cream-strip-text span { color: var(--navy); }
        .cream-strip-meta { font-size: 0.8rem; color: var(--ink-soft); }

        .cream-strip-dots { display: flex; align-items: center; gap: 16px; }
        .csd-item { display: flex; align-items: center; gap: 6px; font-size: 0.78rem; color: var(--ink-mid); font-weight: 500; }
        .csd-dot { width: 7px; height: 7px; border-radius: 50%; background: var(--navy); }

        /* ─────────────────────────────────
           FOOTER
        ───────────────────────────────── */
        footer {
            border-top: 1px solid var(--border-soft);
            padding: 28px clamp(1.25rem,5vw,4rem);
            max-width: 1360px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }

        .footer-copy { font-size: 0.78rem; color: var(--ink-faint); }
        .footer-links { display: flex; gap: 20px; }
        .footer-links a { font-size: 0.78rem; color: var(--ink-faint); text-decoration: none; transition: color 0.2s; }
        .footer-links a:hover { color: var(--navy); }

        /* ─────────────────────────────────
           REVEAL ANIMATIONS
        ───────────────────────────────── */
        .reveal { opacity: 0; transform: translateY(20px); transition: opacity 0.75s ease, transform 0.75s ease; }
        .reveal.visible { opacity: 1; transform: none; }
        .d1 { transition-delay: 0.08s; }
        .d2 { transition-delay: 0.16s; }
        .d3 { transition-delay: 0.24s; }
        .d4 { transition-delay: 0.32s; }
        .d5 { transition-delay: 0.40s; }

        /* ─────────────────────────────────
           RESPONSIVE
        ───────────────────────────────── */
        @media (max-width: 1024px) {
            .hero-inner { grid-template-columns: 1fr; gap: 56px; }
            .hero-sub, .stats-strip { max-width: 100%; }
            .cta-row, .stats-strip { justify-content: flex-start; }
            .visual-card-main { max-width: 540px; margin: 0 auto; }
            .feat-grid { grid-template-columns: repeat(2,1fr); }
            .preview-inner { grid-template-columns: 1fr; }
            .cta-box { grid-template-columns: 1fr; }
            .cta-actions { flex-direction: row; }
        }

        @media (max-width: 768px) {
            .feat-grid { grid-template-columns: 1fr; }
            .hero-h1 { font-size: clamp(2.2rem,8vw,3rem); }
            .fc-b, .fc-c { display: none; }
            .stats-strip { width: 100%; }
            .dash-kpi { grid-template-columns: repeat(2,1fr); }
            .cta-box { padding: 40px 28px; }
            .cta-actions { flex-direction: column; align-items: stretch; }
            .btn-cta-cream, .btn-cta-ghost { text-align: center; justify-content: center; }
            footer { flex-direction: column; text-align: center; }
            .footer-links { justify-content: center; }
            .cream-strip { flex-direction: column; }
            .nav-right .btn-nav-ghost { display: none; }
        }

        @media (max-width: 480px) {
            .cta-row { flex-direction: column; align-items: stretch; }
            .btn-hero-primary, .btn-hero-secondary { justify-content: center; }
            .hero-inner { text-align: center; }
            .hero-badge { justify-content: center; display: inline-flex; }
            .cta-row { align-items: center; }
            .stats-strip { flex-direction: column; }
            .stat-block { border-right: none; border-bottom: 1px solid var(--border-soft); }
            .stat-block:last-child { border-bottom: none; }
        }
    </style>
</head>
<body>

    <!-- Background -->
    <div class="bg-layer bg-gradient"></div>
    <div class="bg-layer bg-dots"></div>

    <!-- NAVBAR -->
    <nav id="navbar">
        <a href="#" class="nav-logo">
            <div class="logo-mark">
                <svg viewBox="0 0 24 24">
                    <path d="M21 8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16V8z"/>
                    <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
                    <line x1="12" y1="22.08" x2="12" y2="12"/>
                </svg>
            </div>
            <div class="logo-text">
                <div class="logo-name"><em>HIKARI</em> Logistik</div>
                <div class="logo-sub">Warehouse Management System</div>
            </div>
        </a>

        @if (Route::has('login'))
        <div class="nav-right">
            @auth
                <a href="{{ url('/dashboard') }}" class="btn-nav-solid">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="btn-nav-ghost">Masuk</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn-nav-solid">Daftar Akun</a>
                @endif
            @endauth
        </div>
        @endif
    </nav>

    <div class="page">

        <!-- ══════════════ HERO ══════════════ -->
        <section class="hero">
            <div class="hero-inner">

                <!-- LEFT -->
                <div>
                    <div class="hero-badge reveal">
                        <span class="badge-pill">HIKARI</span>
                        Sistem Gudang Logistik
                        <span class="live-dot"></span>
                    </div>

                    <h1 class="hero-h1 reveal d1">
                        Gudang Lebih<br>
                        <span class="line-blue">Cerdas,</span> Bisnis<br>
                        <span class="line-cream-bg">Lebih Maju.</span>
                    </h1>

                    <p class="hero-sub reveal d2">
                        Platform manajemen gudang terintegrasi untuk MBG. Pantau stok, arus barang, dan laporan operasional secara akurat dan real-time dari mana saja.
                    </p>

                    <div class="cta-row reveal d3">
                        <a href="{{ route('login') }}" class="btn-hero-primary">
                            <span>Masuk ke Sistem</span>
                            <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 8h10M9 4l4 4-4 4"/>
                            </svg>
                        </a>
                        <button class="btn-hero-secondary" onclick="document.getElementById('fitur').scrollIntoView({behavior:'smooth'})">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="10 8 16 12 10 16 10 8"/></svg>
                            Lihat Fitur
                        </button>
                    </div>

                    <div class="stats-strip reveal d4">
                        <div class="stat-block">
                            <div class="stat-num">99.9%</div>
                            <div class="stat-lbl">Uptime</div>
                        </div>
                        <div class="stat-block">
                            <div class="stat-num">Live</div>
                            <div class="stat-lbl">Update Data</div>
                        </div>
                        <div class="stat-block">
                            <div class="stat-num">Multi</div>
                            <div class="stat-lbl">Role & User</div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT — VISUAL -->
                <div class="hero-visual reveal d2">
                    <!-- Floating chips -->
                    <div class="float-chip fc-a">
                        <div class="chip-label">Stok Masuk</div>
                        <div class="chip-val">+1,240 item</div>
                        <span class="chip-tag chip-green">↑ 12.4%</span>
                    </div>
                    <div class="float-chip fc-b">
                        <div class="chip-label">Efisiensi</div>
                        <div class="chip-val">94.7%</div>
                        <span class="chip-tag chip-blue">Excellent</span>
                    </div>
                    <div class="float-chip fc-c">
                        <div class="chip-label">Nilai Stok</div>
                        <div class="chip-val">Rp 2.4M</div>
                        <span class="chip-tag chip-yellow">Update</span>
                    </div>

                    <div class="visual-card-main">
                        <div class="vc-topbar">
                            <div class="vc-topbar-title">Ringkasan Gudang</div>
                            <div class="vc-status">
                                <span class="vc-status-dot"></span> Operasional
                            </div>
                        </div>

                        <div class="vc-metrics">
                            <div class="vc-metric">
                                <div class="vc-metric-val">12,840</div>
                                <div class="vc-metric-lbl">Total SKU</div>
                                <div class="vc-metric-chg">↑ 8.4%</div>
                            </div>
                            <div class="vc-metric">
                                <div class="vc-metric-val">284</div>
                                <div class="vc-metric-lbl">Transaksi</div>
                                <div class="vc-metric-chg">↑ 3.1%</div>
                            </div>
                            <div class="vc-metric">
                                <div class="vc-metric-val">18</div>
                                <div class="vc-metric-lbl">Kategori</div>
                                <div class="vc-metric-chg" style="color:#2563eb">Aktif</div>
                            </div>
                        </div>

                        <div class="vc-progress-list">
                            <div class="vc-progress-item">
                                <div class="vc-pi-head">
                                    <span class="vc-pi-name">Semen & Material</span>
                                    <span class="vc-pi-val">82%</span>
                                </div>
                                <div class="vc-pi-track"><div class="vc-pi-fill" style="width:82%"></div></div>
                            </div>
                            <div class="vc-progress-item">
                                <div class="vc-pi-head">
                                    <span class="vc-pi-name">Besi & Logam</span>
                                    <span class="vc-pi-val">67%</span>
                                </div>
                                <div class="vc-pi-track"><div class="vc-pi-fill" style="width:67%"></div></div>
                            </div>
                            <div class="vc-progress-item">
                                <div class="vc-pi-head">
                                    <span class="vc-pi-name">Cat & Finishing</span>
                                    <span class="vc-pi-val">45%</span>
                                </div>
                                <div class="vc-pi-track"><div class="vc-pi-fill" style="width:45%"></div></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>

        <!-- ══════════════ CREAM BAND ══════════════ -->
        <div class="cream-band">
            <div class="cream-strip reveal">
                <div>
                    <div class="cream-strip-text">Sistem siap digunakan oleh seluruh tim <span>MBG Logistik</span></div>
                    <div class="cream-strip-meta">Akses multi-perangkat · responsif di HP, tablet, dan komputer</div>
                </div>
                <div class="cream-strip-dots">
                    <div class="csd-item"><span class="csd-dot"></span> Admin</div>
                    <div class="csd-item"><span class="csd-dot"></span> Supervisor</div>
                    <div class="csd-item"><span class="csd-dot"></span> Operator</div>
                    <div class="csd-item"><span class="csd-dot"></span> Pelaporan</div>
                </div>
            </div>
        </div>

        <!-- ══════════════ FITUR ══════════════ -->
        <section class="section-wrap" id="fitur">
            <div class="reveal">
                <div class="section-eyebrow">Fitur Unggulan</div>
                <h2 class="section-h2">Satu platform,<br>semua yang dibutuhkan<br>gudang Anda.</h2>
                <p class="section-lead">Dirancang untuk operasional gudang logistik modern dengan antarmuka yang intuitif dan performa tinggi.</p>
            </div>

            <div class="feat-grid">
                <div class="feat-card reveal d1">
                    <div class="feat-icon">
                        <svg viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                    </div>
                    <div class="feat-title">Dashboard Real-time</div>
                    <div class="feat-desc">Ringkasan aktivitas gudang terkini—stok, transaksi, dan alert—tersaji dalam satu layar yang informatif.</div>
                    <span class="feat-tag">Live Monitor</span>
                </div>

                <div class="feat-card highlight reveal d2">
                    <div class="feat-icon">
                        <svg viewBox="0 0 24 24"><path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                    </div>
                    <div class="feat-title">Manajemen SKU & Barcode</div>
                    <div class="feat-desc">Kelola ribuan produk dengan kategori, satuan, barcode, dan atribut lengkap. Pencarian cepat dan akurat.</div>
                    <span class="feat-tag">Produk & Katalog</span>
                </div>

                <div class="feat-card reveal d3">
                    <div class="feat-icon">
                        <svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                    </div>
                    <div class="feat-title">Stok Otomatis</div>
                    <div class="feat-desc">Perhitungan stok masuk & keluar otomatis dengan notifikasi saat stok mendekati batas minimum yang ditentukan.</div>
                    <span class="feat-tag">Auto-update</span>
                </div>

                <div class="feat-card reveal d2">
                    <div class="feat-icon">
                        <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    </div>
                    <div class="feat-title">Laporan & Ekspor</div>
                    <div class="feat-desc">Cetak atau ekspor laporan stok, transaksi, dan keuangan ke Excel atau PDF kapanpun dibutuhkan.</div>
                    <span class="feat-tag">Excel · PDF</span>
                </div>

                <div class="feat-card reveal d3">
                    <div class="feat-icon">
                        <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                    </div>
                    <div class="feat-title">Multi Pengguna & Role</div>
                    <div class="feat-desc">Atur hak akses setiap anggota tim. Admin, supervisor, dan operator mendapatkan tampilan sesuai perannya.</div>
                    <span class="feat-tag">Role-based</span>
                </div>

                <div class="feat-card reveal d4">
                    <div class="feat-icon">
                        <svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                    </div>
                    <div class="feat-title">Keamanan Data</div>
                    <div class="feat-desc">Enkripsi data modern, audit log aktivitas, dan sistem backup otomatis untuk menjaga data tetap aman.</div>
                    <span class="feat-tag">Enterprise</span>
                </div>
            </div>
        </section>

        <!-- ══════════════ PREVIEW ══════════════ -->
        <section class="section-wrap" style="padding-top: 0;" id="preview">
            <div class="preview-inner reveal">
                <div class="preview-text">
                    <div class="section-eyebrow">Antarmuka Modern</div>
                    <h2 class="section-h2" style="font-size: clamp(1.7rem,3vw,2.4rem);">Dashboard yang<br>memudahkan keputusan<br>operasional harian.</h2>
                    <ul class="checklist">
                        <li>
                            <span class="check-icon"><svg viewBox="0 0 12 10"><path d="M1 5l3 3 7-7" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                            Ringkasan stok harian secara visual
                        </li>
                        <li>
                            <span class="check-icon"><svg viewBox="0 0 12 10"><path d="M1 5l3 3 7-7" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                            Histori transaksi masuk & keluar lengkap
                        </li>
                        <li>
                            <span class="check-icon"><svg viewBox="0 0 12 10"><path d="M1 5l3 3 7-7" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                            Notifikasi otomatis stok kritis
                        </li>
                        <li>
                            <span class="check-icon"><svg viewBox="0 0 12 10"><path d="M1 5l3 3 7-7" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                            Laporan keuangan per periode
                        </li>
                        <li>
                            <span class="check-icon"><svg viewBox="0 0 12 10"><path d="M1 5l3 3 7-7" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                            Responsif di HP, tablet, dan komputer
                        </li>
                    </ul>
                </div>

                <!-- Dashboard Mockup -->
                <div class="dash-mockup reveal d2">
                    <div class="dash-titlebar">
                        <div class="dtb-dot dtb-r"></div>
                        <div class="dtb-dot dtb-y"></div>
                        <div class="dtb-dot dtb-g"></div>
                        <div class="dtb-title">HIKARI · Dashboard Gudang</div>
                    </div>
                    <div class="dash-body">
                        <div class="dash-kpi">
                            <div class="kpi-box">
                                <div class="kpi-val">12,840</div>
                                <div class="kpi-lbl">Total Stok</div>
                                <div class="kpi-chg">↑ 8.4%</div>
                            </div>
                            <div class="kpi-box">
                                <div class="kpi-val">284</div>
                                <div class="kpi-lbl">Transaksi</div>
                                <div class="kpi-chg">↑ 3.1%</div>
                            </div>
                            <div class="kpi-box" style="grid-column: span 1;">
                                <div class="kpi-val">18</div>
                                <div class="kpi-lbl">Kategori</div>
                                <div class="kpi-chg" style="color: var(--navy);">Aktif</div>
                            </div>
                        </div>
                        <div class="dash-table">
                            <div class="dt-row hd">
                                <div class="dt-cell">Nama Barang</div>
                                <div class="dt-cell">Qty</div>
                                <div class="dt-cell">Status</div>
                            </div>
                            <div class="dt-row">
                                <div class="dt-cell">Semen Gresik 50kg</div>
                                <div class="dt-cell">340</div>
                                <div class="dt-cell"><span class="status-tag st-in">Masuk</span></div>
                            </div>
                            <div class="dt-row">
                                <div class="dt-cell">Besi Beton 12mm</div>
                                <div class="dt-cell">120</div>
                                <div class="dt-cell"><span class="status-tag st-out">Keluar</span></div>
                            </div>
                            <div class="dt-row">
                                <div class="dt-cell">Cat Tembok 25kg</div>
                                <div class="dt-cell">85</div>
                                <div class="dt-cell"><span class="status-tag st-in">Masuk</span></div>
                            </div>
                            <div class="dt-row">
                                <div class="dt-cell">Pipa PVC 3"</div>
                                <div class="dt-cell">200</div>
                                <div class="dt-cell"><span class="status-tag st-out">Keluar</span></div>
                            </div>
                        </div>
                        <div class="dash-bars">
                            <div class="db-title">Tren 7 Hari Terakhir</div>
                            <div class="db-chart">
                                <div class="db-bar" style="height:40%"></div>
                                <div class="db-bar" style="height:58%"></div>
                                <div class="db-bar" style="height:47%"></div>
                                <div class="db-bar" style="height:72%"></div>
                                <div class="db-bar" style="height:60%"></div>
                                <div class="db-bar active" style="height:88%"></div>
                                <div class="db-bar accent" style="height:100%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ══════════════ CTA ══════════════ -->
        <div class="cta-wrapper">
            <div class="cta-box reveal">
                <div class="cta-box-top"></div>
                <div class="cta-text">
                    <div class="cta-eyebrow">Siap Memulai?</div>
                    <h2 class="cta-h2">Kelola Gudang <span class="cream">MBG</span><br>dengan Lebih Efisien<br>Mulai Sekarang.</h2>
                    <p class="cta-desc">Akses penuh ke seluruh fitur sistem—dari manajemen stok hingga laporan operasional—dalam satu platform yang aman dan andal.</p>
                </div>
                <div class="cta-actions">
                    <a href="{{ route('login') }}" class="btn-cta-cream">
                        Masuk ke Sistem
                        <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 8h10M9 4l4 4-4 4"/></svg>
                    </a>
                    @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn-cta-ghost">Daftar Akun Baru</a>
                    @endif
                </div>
            </div>
        </div>

        <!-- FOOTER -->
        <footer>
            <div class="footer-copy">&copy; 2026 HIKARI Logistik · Warehouse Management System</div>
            <div class="footer-links">
                <a href="#">Privasi</a>
                <a href="#">Bantuan</a>
                <a href="#">Kontak</a>
            </div>
        </footer>

    </div><!-- /.page -->

    <script>
        // Navbar scroll
        const nav = document.getElementById('navbar');
        window.addEventListener('scroll', () => nav.classList.toggle('scrolled', scrollY > 20));

        // Reveal on scroll
        const obs = new IntersectionObserver(entries => {
            entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('visible'); obs.unobserve(e.target); } });
        }, { threshold: 0.1 });
        document.querySelectorAll('.reveal').forEach(el => obs.observe(el));

        // Instant hero
        setTimeout(() => document.querySelectorAll('.hero .reveal').forEach(el => el.classList.add('visible')), 80);
    </script>

</body>
</html>