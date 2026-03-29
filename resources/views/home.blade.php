<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>RecoTicket — Descubrí los mejores eventos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; }

        :root {
            --purple: #7c3aed;
            --violet: #8b5cf6;
            --pink: #ec4899;
            --cyan: #06b6d4;
            --gold: #f59e0b;
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Space Grotesk', sans-serif;
            background: #06000f;
            color: #fff;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ── Noise texture ─────────────────────────────── */
        body::after {
            content: '';
            position: fixed;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='300' height='300'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.75' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='300' height='300' filter='url(%23n)' opacity='0.04'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 1000;
            opacity: 0.5;
        }

        /* ── Animated background orbs ──────────────────── */
        .orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(90px);
            opacity: 0.28;
            pointer-events: none;
            z-index: 0;
            will-change: transform;
        }

        .orb-1 {
            width: 700px; height: 700px;
            background: radial-gradient(circle at 40% 40%, #7c3aed, #4f46e5 60%, transparent);
            top: -250px; left: -150px;
            animation: orb1 14s ease-in-out infinite;
        }
        .orb-2 {
            width: 550px; height: 550px;
            background: radial-gradient(circle at 60% 60%, #ec4899, #9333ea 60%, transparent);
            top: 25%; right: -180px;
            animation: orb2 18s ease-in-out infinite;
        }
        .orb-3 {
            width: 450px; height: 450px;
            background: radial-gradient(circle at 50% 50%, #0ea5e9, #06b6d4 60%, transparent);
            bottom: 5%; left: 15%;
            animation: orb3 22s ease-in-out infinite;
        }
        .orb-4 {
            width: 350px; height: 350px;
            background: radial-gradient(circle at 50% 50%, #f59e0b, #ef4444 60%, transparent);
            top: 55%; left: 55%;
            animation: orb4 12s ease-in-out infinite;
        }
        .orb-5 {
            width: 280px; height: 280px;
            background: radial-gradient(circle at 50% 50%, #10b981, #06b6d4 60%, transparent);
            top: 80%; right: 10%;
            animation: orb5 16s ease-in-out infinite;
        }

        @keyframes orb1 {
            0%,100% { transform: translate(0,0) scale(1); }
            33%      { transform: translate(80px,60px) scale(1.12); }
            66%      { transform: translate(-40px,100px) scale(0.92); }
        }
        @keyframes orb2 {
            0%,100% { transform: translate(0,0) scale(1); }
            40%      { transform: translate(-100px,70px) scale(1.18); }
            70%      { transform: translate(50px,-60px) scale(0.88); }
        }
        @keyframes orb3 {
            0%,100% { transform: translate(0,0) scale(1); }
            50%      { transform: translate(120px,-80px) scale(1.15); }
        }
        @keyframes orb4 {
            0%,100% { transform: translate(0,0) scale(1); }
            50%      { transform: translate(-70px,50px) scale(1.2); }
        }
        @keyframes orb5 {
            0%,100% { transform: translate(0,0) scale(1); }
            50%      { transform: translate(60px,-90px) scale(1.1); }
        }

        /* ── Glass utilities ───────────────────────────── */
        .glass {
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.09);
        }
        .glass-nav {
            background: rgba(6,0,15,0.55);
            backdrop-filter: blur(28px) saturate(180%);
            -webkit-backdrop-filter: blur(28px) saturate(180%);
            border-bottom: 1px solid rgba(255,255,255,0.07);
        }
        .glass-card {
            background: rgba(255,255,255,0.055);
            backdrop-filter: blur(18px) saturate(160%);
            -webkit-backdrop-filter: blur(18px) saturate(160%);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 20px;
            overflow: hidden;
            transition: transform 0.35s cubic-bezier(.22,.68,0,1.2),
                        box-shadow 0.35s ease,
                        border-color 0.35s ease,
                        background 0.35s ease;
        }
        .glass-card:hover {
            transform: translateY(-8px) scale(1.01);
            background: rgba(255,255,255,0.1);
            border-color: rgba(139,92,246,0.45);
            box-shadow: 0 24px 64px rgba(124,58,237,0.35),
                        0 0 0 1px rgba(139,92,246,0.15),
                        inset 0 1px 0 rgba(255,255,255,0.1);
        }
        .glass-input {
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.12);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            color: #fff;
            border-radius: 14px;
            padding: 12px 18px;
            font-size: 14px;
            font-family: 'Space Grotesk', sans-serif;
            transition: border-color 0.25s, background 0.25s, box-shadow 0.25s;
            width: 100%;
        }
        .glass-input::placeholder { color: rgba(255,255,255,0.38); }
        .glass-input:focus {
            outline: none;
            border-color: rgba(139,92,246,0.65);
            background: rgba(255,255,255,0.11);
            box-shadow: 0 0 0 3px rgba(139,92,246,0.18), 0 0 24px rgba(139,92,246,0.15);
        }
        .glass-select {
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.12);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            color: #fff;
            border-radius: 14px;
            padding: 12px 18px;
            font-size: 14px;
            font-family: 'Space Grotesk', sans-serif;
            cursor: pointer;
            transition: border-color 0.25s, background 0.25s, box-shadow 0.25s;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='rgba(255,255,255,0.5)' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
            padding-right: 44px;
        }
        .glass-select option { background: #1a0033; color: #fff; }
        .glass-select:focus {
            outline: none;
            border-color: rgba(139,92,246,0.65);
            box-shadow: 0 0 0 3px rgba(139,92,246,0.18);
        }

        /* ── Gradient text ─────────────────────────────── */
        .text-gradient {
            background: linear-gradient(135deg, #c4b5fd 0%, #f9a8d4 40%, #93c5fd 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            background-size: 200% 200%;
            animation: gradientShift 6s ease infinite;
        }
        .text-gradient-gold {
            background: linear-gradient(135deg, #fde68a, #f59e0b, #fbbf24);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        @keyframes gradientShift {
            0%,100% { background-position: 0% 50%; }
            50%      { background-position: 100% 50%; }
        }

        /* ── Buttons ───────────────────────────────────── */
        .btn-primary {
            background: linear-gradient(135deg, #7c3aed, #6d28d9);
            border: 1px solid rgba(139,92,246,0.4);
            box-shadow: 0 0 20px rgba(124,58,237,0.45), inset 0 1px 0 rgba(255,255,255,0.12);
            color: #fff;
            border-radius: 14px;
            padding: 12px 28px;
            font-weight: 600;
            font-size: 14px;
            font-family: 'Space Grotesk', sans-serif;
            cursor: pointer;
            transition: all 0.28s cubic-bezier(.22,.68,0,1.2);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            white-space: nowrap;
        }
        .btn-primary:hover {
            box-shadow: 0 0 40px rgba(124,58,237,0.75), inset 0 1px 0 rgba(255,255,255,0.2);
            transform: translateY(-2px) scale(1.02);
        }
        .btn-gold {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            border: 1px solid rgba(245,158,11,0.4);
            box-shadow: 0 0 20px rgba(245,158,11,0.35), inset 0 1px 0 rgba(255,255,255,0.15);
            color: #1a0a00;
            border-radius: 14px;
            padding: 12px 28px;
            font-weight: 700;
            font-size: 14px;
            font-family: 'Space Grotesk', sans-serif;
            cursor: pointer;
            transition: all 0.28s cubic-bezier(.22,.68,0,1.2);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            white-space: nowrap;
        }
        .btn-gold:hover {
            box-shadow: 0 0 40px rgba(245,158,11,0.65);
            transform: translateY(-2px) scale(1.02);
        }
        .btn-ghost {
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.12);
            backdrop-filter: blur(10px);
            color: rgba(255,255,255,0.85);
            border-radius: 14px;
            padding: 11px 24px;
            font-weight: 500;
            font-size: 14px;
            font-family: 'Space Grotesk', sans-serif;
            cursor: pointer;
            transition: all 0.25s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            white-space: nowrap;
        }
        .btn-ghost:hover {
            background: rgba(255,255,255,0.12);
            border-color: rgba(255,255,255,0.22);
            color: #fff;
        }

        /* ── Nav link ──────────────────────────────────── */
        .nav-link {
            color: rgba(255,255,255,0.65);
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            padding: 6px 12px;
            border-radius: 10px;
            transition: color 0.2s, background 0.2s;
        }
        .nav-link:hover {
            color: #fff;
            background: rgba(255,255,255,0.07);
        }

        /* ── Hero badge ────────────────────────────────── */
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 7px 18px;
            background: rgba(124,58,237,0.18);
            border: 1px solid rgba(139,92,246,0.38);
            border-radius: 100px;
            font-size: 12.5px;
            font-weight: 500;
            color: #c4b5fd;
            letter-spacing: 0.03em;
        }
        .hero-badge-dot {
            width: 6px; height: 6px;
            background: #a78bfa;
            border-radius: 50%;
            animation: dotPulse 2s ease-in-out infinite;
        }
        @keyframes dotPulse {
            0%,100% { transform: scale(1); opacity: 1; box-shadow: 0 0 0 0 rgba(167,139,250,0.5); }
            50%      { transform: scale(1.2); opacity: 0.8; box-shadow: 0 0 0 5px rgba(167,139,250,0); }
        }

        /* ── Category pills ────────────────────────────── */
        .cat-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 18px;
            border-radius: 100px;
            font-size: 13px;
            font-weight: 500;
            border: 1px solid rgba(255,255,255,0.1);
            background: rgba(255,255,255,0.06);
            color: rgba(255,255,255,0.65);
            text-decoration: none;
            transition: all 0.22s ease;
            white-space: nowrap;
            cursor: pointer;
        }
        .cat-pill:hover, .cat-pill.active {
            background: rgba(124,58,237,0.35);
            border-color: rgba(139,92,246,0.55);
            color: #e9d5ff;
            box-shadow: 0 0 16px rgba(124,58,237,0.3);
        }
        .cat-pill.all-active {
            background: rgba(124,58,237,0.35);
            border-color: rgba(139,92,246,0.55);
            color: #e9d5ff;
        }

        /* ── Badge featured ────────────────────────────── */
        .badge-featured {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 10px;
            border-radius: 100px;
            font-size: 10.5px;
            font-weight: 600;
            letter-spacing: 0.04em;
            background: rgba(245,158,11,0.2);
            border: 1px solid rgba(245,158,11,0.45);
            color: #fbbf24;
        }
        .badge-cat {
            display: inline-flex;
            align-items: center;
            padding: 3px 10px;
            border-radius: 100px;
            font-size: 11px;
            font-weight: 500;
            background: rgba(124,58,237,0.25);
            border: 1px solid rgba(139,92,246,0.35);
            color: #c4b5fd;
        }
        .badge-free {
            display: inline-flex;
            align-items: center;
            padding: 3px 10px;
            border-radius: 100px;
            font-size: 11px;
            font-weight: 600;
            background: rgba(16,185,129,0.2);
            border: 1px solid rgba(16,185,129,0.4);
            color: #6ee7b7;
        }

        /* ── Stats ─────────────────────────────────────── */
        .stat-card {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            backdrop-filter: blur(12px);
            border-radius: 20px;
            padding: 28px 24px;
            text-align: center;
            transition: transform 0.3s ease, border-color 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-4px);
            border-color: rgba(139,92,246,0.3);
        }

        /* ── Section divider ───────────────────────────── */
        .divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(139,92,246,0.4), rgba(236,72,153,0.3), transparent);
            margin: 0;
        }

        /* ── Animations ────────────────────────────────── */
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(36px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }
        @keyframes scaleIn {
            from { opacity: 0; transform: scale(0.94); }
            to   { opacity: 1; transform: scale(1); }
        }

        .anim-up   { animation: slideUp 0.75s cubic-bezier(.22,.68,0,1.2) both; }
        .anim-up-1 { animation: slideUp 0.75s 0.1s cubic-bezier(.22,.68,0,1.2) both; }
        .anim-up-2 { animation: slideUp 0.75s 0.22s cubic-bezier(.22,.68,0,1.2) both; }
        .anim-up-3 { animation: slideUp 0.75s 0.34s cubic-bezier(.22,.68,0,1.2) both; }
        .anim-fade { animation: fadeIn 1s 0.5s both; }
        .anim-scale{ animation: scaleIn 0.6s cubic-bezier(.22,.68,0,1.2) both; }

        /* Card stagger via CSS counter trick */
        .card-grid .glass-card:nth-child(1) { animation: slideUp 0.6s 0.05s both; }
        .card-grid .glass-card:nth-child(2) { animation: slideUp 0.6s 0.12s both; }
        .card-grid .glass-card:nth-child(3) { animation: slideUp 0.6s 0.19s both; }
        .card-grid .glass-card:nth-child(4) { animation: slideUp 0.6s 0.26s both; }
        .card-grid .glass-card:nth-child(5) { animation: slideUp 0.6s 0.33s both; }
        .card-grid .glass-card:nth-child(6) { animation: slideUp 0.6s 0.40s both; }
        .card-grid .glass-card:nth-child(7) { animation: slideUp 0.6s 0.47s both; }
        .card-grid .glass-card:nth-child(8) { animation: slideUp 0.6s 0.54s both; }
        .card-grid .glass-card:nth-child(n+9) { animation: slideUp 0.6s 0.6s both; }

        /* ── Image overlay ─────────────────────────────── */
        .card-img-wrap { position: relative; overflow: hidden; }
        .card-img-wrap img {
            width: 100%; height: 200px;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        .glass-card:hover .card-img-wrap img { transform: scale(1.07); }
        .card-img-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(6,0,15,0.7) 0%, transparent 50%);
        }

        /* ── Placeholder image ─────────────────────────── */
        .card-placeholder {
            width: 100%; height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            position: relative;
        }

        /* ── Empty state ───────────────────────────────── */
        .empty-state {
            text-align: center;
            padding: 80px 20px;
        }
        .empty-icon {
            font-size: 64px;
            display: block;
            margin-bottom: 20px;
            filter: grayscale(0.3);
        }

        /* ── Pagination override ───────────────────────── */
        nav[role="navigation"] span, nav[role="navigation"] a {
            background: rgba(255,255,255,0.07) !important;
            border-color: rgba(255,255,255,0.1) !important;
            color: rgba(255,255,255,0.7) !important;
            border-radius: 10px !important;
            margin: 0 2px !important;
            font-family: 'Space Grotesk', sans-serif !important;
            font-size: 13px !important;
            transition: all 0.2s !important;
        }
        nav[role="navigation"] a:hover {
            background: rgba(124,58,237,0.3) !important;
            border-color: rgba(139,92,246,0.5) !important;
            color: #fff !important;
        }
        nav[role="navigation"] [aria-current="page"] span {
            background: rgba(124,58,237,0.45) !important;
            border-color: rgba(139,92,246,0.6) !important;
            color: #fff !important;
        }

        /* ── Scrollbar ─────────────────────────────────── */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: rgba(255,255,255,0.02); }
        ::-webkit-scrollbar-thumb { background: rgba(139,92,246,0.45); border-radius: 4px; }

        /* ── Mobile responsive ─────────────────────────── */
        @media (max-width: 768px) {
            .orb-1 { width: 350px; height: 350px; }
            .orb-2 { width: 300px; height: 300px; }
            .orb-3 { width: 250px; height: 250px; }
            .orb-4, .orb-5 { display: none; }
        }

        /* ── CTA section ───────────────────────────────── */
        .cta-section {
            background: rgba(124,58,237,0.1);
            border: 1px solid rgba(139,92,246,0.2);
            border-radius: 28px;
            padding: 60px 40px;
            text-align: center;
            backdrop-filter: blur(20px);
            position: relative;
            overflow: hidden;
        }
        .cta-section::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse at 50% 0%, rgba(139,92,246,0.2), transparent 70%);
            pointer-events: none;
        }

        /* ── z-index ───────────────────────────────────── */
        .z-nav  { position: relative; z-index: 50; }
        .z-main { position: relative; z-index: 10; }
    </style>
</head>
<body>

    {{-- ── BACKGROUND ORBS ────────────────────────────────────────── --}}
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
    <div class="orb orb-4"></div>
    <div class="orb orb-5"></div>

    {{-- ── NAVIGATION ─────────────────────────────────────────────── --}}
    <nav class="glass-nav sticky top-0 z-nav">
        <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between gap-4">

            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 shrink-0">
                <span class="text-2xl leading-none">🎟</span>
                <span class="text-white font-bold text-lg tracking-tight">
                    Reco<span class="text-gradient">Ticket</span>
                </span>
            </a>

            {{-- Nav Links --}}
            <div class="hidden md:flex items-center gap-1">
                <a href="{{ route('home') }}" class="nav-link">Eventos</a>
                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="nav-link">Admin</a>
                    @elseif(auth()->user()->isOrganizer())
                        <a href="{{ route('organizer.dashboard') }}" class="nav-link">Mi Panel</a>
                        <a href="{{ route('organizer.scan') }}" class="nav-link">Escanear QR</a>
                    @else
                        <a href="{{ route('buyer.dashboard') }}" class="nav-link">Mis Entradas</a>
                        <a href="{{ route('buyer.orders.index') }}" class="nav-link">Mis Órdenes</a>
                    @endif
                @endauth
            </div>

            {{-- Auth Actions --}}
            <div class="flex items-center gap-3 shrink-0">
                @guest
                    <a href="{{ route('login') }}" class="btn-ghost" style="padding:9px 20px;">Iniciar sesión</a>
                    <a href="{{ route('register') }}" class="btn-gold" style="padding:9px 20px;">Registrarse</a>
                @endguest
                @auth
                    <span class="text-sm text-white/50 hidden sm:block font-medium">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn-ghost" style="padding:8px 18px;font-size:13px;">Salir</button>
                    </form>
                @endauth
            </div>
        </div>
    </nav>

    {{-- ── MAIN CONTENT ────────────────────────────────────────────── --}}
    <main class="z-main">

        {{-- ── HERO SECTION ──────────────────────────────────────────── --}}
        <section class="relative px-6 pt-20 pb-16 max-w-7xl mx-auto text-center">

            {{-- Badge --}}
            <div class="flex justify-center mb-7 anim-up">
                <div class="hero-badge">
                    <span class="hero-badge-dot"></span>
                    Argentina · Eventos en vivo
                </div>
            </div>

            {{-- Headline --}}
            <h1 class="anim-up-1 font-extrabold leading-[1.08] tracking-tight mb-6"
                style="font-size: clamp(2.6rem, 7vw, 5.2rem);">
                Viví la experiencia<br>
                <span class="text-gradient">que te merece&acute;s</span>
            </h1>

            {{-- Subtitle --}}
            <p class="anim-up-2 text-white/55 max-w-xl mx-auto mb-10 leading-relaxed"
               style="font-size:clamp(15px,2vw,18px);">
                Encontrá conciertos, festivales, teatro y más.
                Comprá tus entradas en segundos — sin filas, sin complicaciones.
            </p>

            {{-- Hero CTAs --}}
            <div class="anim-up-3 flex flex-wrap justify-center gap-4 mb-14">
                <a href="#eventos" class="btn-primary" style="padding:14px 32px;font-size:15px;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                    Explorar eventos
                </a>
                @guest
                    <a href="{{ route('register') }}" class="btn-ghost" style="padding:14px 32px;font-size:15px;">
                        Crear cuenta gratis →
                    </a>
                @endguest
            </div>

            {{-- Stats row --}}
            <div class="anim-fade grid grid-cols-3 gap-4 max-w-lg mx-auto">
                <div class="stat-card">
                    <div class="text-2xl font-bold text-gradient-gold mb-1">+500</div>
                    <div class="text-xs text-white/45 font-medium">Eventos activos</div>
                </div>
                <div class="stat-card">
                    <div class="text-2xl font-bold text-gradient mb-1">+50k</div>
                    <div class="text-xs text-white/45 font-medium">Entradas vendidas</div>
                </div>
                <div class="stat-card">
                    <div class="text-2xl font-bold" style="color:#6ee7b7;">24 hs</div>
                    <div class="text-xs text-white/45 font-medium">Soporte disponible</div>
                </div>
            </div>
        </section>

        <div class="divider"></div>

        {{-- ── SEARCH + FILTER SECTION ───────────────────────────────── --}}
        <section class="px-6 py-10 max-w-7xl mx-auto" id="eventos">

            <form method="GET" action="{{ route('home') }}" id="filterForm">

                {{-- Search bar --}}
                <div class="glass rounded-2xl p-6 mb-6 anim-scale">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <div class="relative flex-1">
                            <svg class="absolute left-4 top-1/2 -translate-y-1/2 text-white/35 pointer-events-none"
                                 width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/>
                            </svg>
                            <input
                                type="text"
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="Buscar eventos, artistas, lugares…"
                                class="glass-input"
                                style="padding-left:44px;"
                                autocomplete="off"
                            >
                        </div>
                        <select name="category" class="glass-select" style="min-width:190px;" onchange="document.getElementById('filterForm').submit()">
                            <option value="">Todas las categorías</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->slug }}" @selected(request('category') === $cat->slug)>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn-primary" style="padding:12px 24px;">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                            Buscar
                        </button>
                        @if(request()->hasAny(['search', 'category']))
                            <a href="{{ route('home') }}" class="btn-ghost" style="padding:12px 18px;">
                                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
                                Limpiar
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Category pills --}}
                @if($categories->isNotEmpty())
                    <div class="flex flex-wrap gap-2 mb-2">
                        <a href="{{ route('home') }}" class="cat-pill {{ !request('category') && !request('search') ? 'all-active' : '' }}">
                            Todos
                        </a>
                        @foreach($categories as $cat)
                            <a href="{{ route('home', ['category' => $cat->slug]) }}"
                               class="cat-pill {{ request('category') === $cat->slug ? 'active' : '' }}">
                                {{ $cat->name }}
                            </a>
                        @endforeach
                    </div>
                @endif

            </form>
        </section>

        {{-- ── EVENTS GRID ────────────────────────────────────────────── --}}
        <section class="px-6 pb-16 max-w-7xl mx-auto">

            {{-- Section header --}}
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-2xl font-bold text-white mb-1">
                        @if(request('search'))
                            Resultados para <span class="text-gradient">"{{ request('search') }}"</span>
                        @elseif(request('category'))
                            @php $activeCat = $categories->firstWhere('slug', request('category')); @endphp
                            Categoría · <span class="text-gradient">{{ $activeCat?->name ?? request('category') }}</span>
                        @else
                            Próximos eventos
                        @endif
                    </h2>
                    <p class="text-white/40 text-sm">
                        {{ $events->total() }} evento{{ $events->total() !== 1 ? 's' : '' }} encontrado{{ $events->total() !== 1 ? 's' : '' }}
                    </p>
                </div>
            </div>

            {{-- Flash messages --}}
            @if(session('success'))
                <div class="mb-6 glass rounded-xl px-5 py-3.5 border border-emerald-500/30 text-emerald-300 text-sm flex items-center gap-3">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="10"/></svg>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 glass rounded-xl px-5 py-3.5 border border-red-500/30 text-red-300 text-sm flex items-center gap-3">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6"/></svg>
                    {{ session('error') }}
                </div>
            @endif

            {{-- Empty state --}}
            @if($events->isEmpty())
                <div class="empty-state glass rounded-3xl">
                    <span class="empty-icon">🎭</span>
                    <h3 class="text-xl font-semibold text-white mb-3">No se encontraron eventos</h3>
                    <p class="text-white/45 text-sm mb-6 max-w-xs mx-auto">
                        Intentá ajustar tu búsqueda o explorá todas las categorías disponibles.
                    </p>
                    <a href="{{ route('home') }}" class="btn-primary">Ver todos los eventos</a>
                </div>

            {{-- Events grid --}}
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5 card-grid">
                    @foreach($events as $event)
                    <a href="{{ route('events.show', $event->slug) }}" class="glass-card group" style="text-decoration:none;display:block;">

                        {{-- Cover image --}}
                        <div class="card-img-wrap">
                            @if($event->cover_image)
                                <img src="{{ $event->cover_image }}" alt="{{ $event->title }}" loading="lazy">
                            @else
                                @php
                                    $gradients = [
                                        'from-violet-600 to-indigo-700',
                                        'from-pink-600 to-purple-700',
                                        'from-cyan-600 to-blue-700',
                                        'from-orange-500 to-pink-600',
                                        'from-emerald-600 to-cyan-700',
                                        'from-rose-600 to-violet-700',
                                    ];
                                    $emojis = ['🎵','🎸','🎤','🎭','🎪','🎨','🎬','🥁','🎺','🎻'];
                                    $gradIdx = $loop->index % count($gradients);
                                    $emojiIdx = $loop->index % count($emojis);
                                @endphp
                                <div class="card-placeholder bg-gradient-to-br {{ $gradients[$gradIdx] }}">
                                    {{ $emojis[$emojiIdx] }}
                                </div>
                            @endif
                            <div class="card-img-overlay"></div>

                            {{-- Badges overlaid on image --}}
                            <div class="absolute top-3 left-3 flex flex-wrap gap-1.5">
                                @if($event->featured)
                                    <span class="badge-featured">⭐ Destacado</span>
                                @endif
                                @if(isset($event->minPrice) && $event->minPrice === 0)
                                    <span class="badge-free">Gratis</span>
                                @endif
                            </div>
                        </div>

                        {{-- Card body --}}
                        <div class="p-4">
                            <h3 class="font-semibold text-white text-sm leading-snug mb-2 line-clamp-2 group-hover:text-violet-300 transition-colors">
                                {{ $event->title }}
                            </h3>

                            {{-- Date --}}
                            @if($event->start_date)
                                <div class="flex items-center gap-1.5 text-violet-300/80 text-xs mb-1.5">
                                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                                    {{ $event->start_date->format('d M Y · H:i') }}
                                </div>
                            @endif

                            {{-- Venue --}}
                            @if($event->venue)
                                <div class="flex items-center gap-1.5 text-white/45 text-xs mb-3">
                                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                    {{ $event->venue->city }}{{ $event->venue->state ? ', '.$event->venue->state : '' }}
                                </div>
                            @endif

                            {{-- Footer row: category + price --}}
                            <div class="flex items-center justify-between gap-2 flex-wrap">
                                @if($event->category)
                                    <span class="badge-cat">{{ $event->category->name }}</span>
                                @endif

                                @if(isset($event->minPrice) && $event->minPrice !== null)
                                    @if($event->minPrice == 0)
                                        <span class="text-emerald-400 text-xs font-bold">Gratis</span>
                                    @else
                                        <span class="text-white/80 text-xs font-bold">
                                            Desde ${{ number_format($event->minPrice, 0, ',', '.') }}
                                        </span>
                                    @endif
                                @endif
                            </div>
                        </div>

                        {{-- Bottom glow on hover --}}
                        <div class="h-0.5 w-full opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                             style="background:linear-gradient(90deg,transparent,rgba(139,92,246,0.7),transparent);"></div>
                    </a>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if($events->hasPages())
                    <div class="mt-10 flex justify-center">
                        {{ $events->links() }}
                    </div>
                @endif
            @endif
        </section>

        <div class="divider" style="max-width:1280px;margin:0 auto;"></div>

        {{-- ── CTA SECTION ────────────────────────────────────────────── --}}
        @guest
        <section class="px-6 py-16 max-w-4xl mx-auto">
            <div class="cta-section">
                <div class="hero-badge mb-6" style="display:inline-flex;">
                    <span class="hero-badge-dot"></span>
                    Unite a la comunidad
                </div>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-white mb-4 tracking-tight">
                    ¿Organizás eventos?<br>
                    <span class="text-gradient">Empezá gratis hoy.</span>
                </h2>
                <p class="text-white/50 mb-8 max-w-md mx-auto text-sm leading-relaxed">
                    Creá y gestioná tus eventos, vendé entradas con QR y accedé a estadísticas en tiempo real.
                </p>
                <div class="flex flex-wrap justify-center gap-4">
                    <a href="{{ route('register') }}" class="btn-gold" style="padding:14px 32px;font-size:15px;">
                        Crear cuenta gratis
                    </a>
                    <a href="{{ route('login') }}" class="btn-ghost" style="padding:14px 32px;font-size:15px;">
                        Iniciar sesión
                    </a>
                </div>
            </div>
        </section>
        @endguest

    </main>

    {{-- ── FOOTER ──────────────────────────────────────────────────── --}}
    <footer class="glass-nav z-nav mt-8">
        <div class="max-w-7xl mx-auto px-6 py-8">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <span class="text-xl">🎟</span>
                    <span class="font-bold text-white">Reco<span class="text-gradient">Ticket</span></span>
                </div>
                <p class="text-white/35 text-xs text-center">
                    © {{ date('Y') }} RecoTicket · Hecho con ❤️ en Argentina
                </p>
                <div class="flex items-center gap-5">
                    <a href="{{ route('home') }}" class="text-white/40 text-xs hover:text-white/70 transition">Eventos</a>
                    @guest
                        <a href="{{ route('login') }}" class="text-white/40 text-xs hover:text-white/70 transition">Ingresar</a>
                        <a href="{{ route('register') }}" class="text-white/40 text-xs hover:text-white/70 transition">Registrarse</a>
                    @endguest
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Smooth anchor scroll for "Explorar eventos"
        document.querySelectorAll('a[href^="#"]').forEach(a => {
            a.addEventListener('click', e => {
                const target = document.querySelector(a.getAttribute('href'));
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });

        // Intersection Observer for lazy card animations
        if ('IntersectionObserver' in window) {
            const io = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.animationPlayState = 'running';
                        io.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1 });

            document.querySelectorAll('.glass-card').forEach(card => {
                card.style.animationPlayState = 'paused';
                io.observe(card);
            });
        }
    </script>
</body>
</html>
