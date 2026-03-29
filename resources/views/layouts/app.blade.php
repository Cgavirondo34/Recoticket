<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'RecoTicket') — RecoTicket</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; }

        body {
            font-family: 'Space Grotesk', sans-serif;
            background: #06000f;
            color: #fff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        /* ── Subtle background ─────────────────────────── */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background:
                radial-gradient(ellipse 80% 60% at 15% -10%, rgba(124,58,237,0.18) 0%, transparent 60%),
                radial-gradient(ellipse 60% 50% at 90% 20%, rgba(236,72,153,0.12) 0%, transparent 55%),
                radial-gradient(ellipse 50% 60% at 50% 100%, rgba(6,182,212,0.08) 0%, transparent 60%);
            pointer-events: none;
            z-index: 0;
        }

        /* ── Noise overlay ─────────────────────────────── */
        body::after {
            content: '';
            position: fixed;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='200' height='200'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='200' height='200' filter='url(%23n)' opacity='0.035'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 1;
            opacity: 0.45;
        }

        /* ── Glass nav ─────────────────────────────────── */
        .glass-nav {
            background: rgba(6,0,15,0.6);
            backdrop-filter: blur(28px) saturate(180%);
            -webkit-backdrop-filter: blur(28px) saturate(180%);
            border-bottom: 1px solid rgba(255,255,255,0.07);
            position: sticky;
            top: 0;
            z-index: 50;
        }

        /* ── Text gradient ─────────────────────────────── */
        .text-gradient {
            background: linear-gradient(135deg, #c4b5fd 0%, #f9a8d4 40%, #93c5fd 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* ── Nav link ──────────────────────────────────── */
        .nav-link {
            color: rgba(255,255,255,0.6);
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            padding: 6px 12px;
            border-radius: 10px;
            transition: color 0.2s, background 0.2s;
        }
        .nav-link:hover { color: #fff; background: rgba(255,255,255,0.07); }

        /* ── Buttons ───────────────────────────────────── */
        .btn-ghost {
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.12);
            color: rgba(255,255,255,0.8);
            border-radius: 12px;
            padding: 8px 18px;
            font-size: 13px;
            font-weight: 500;
            font-family: 'Space Grotesk', sans-serif;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-ghost:hover { background: rgba(255,255,255,0.12); color: #fff; }
        .btn-primary {
            background: linear-gradient(135deg, #7c3aed, #6d28d9);
            border: 1px solid rgba(139,92,246,0.4);
            color: #fff;
            border-radius: 12px;
            padding: 8px 20px;
            font-size: 13px;
            font-weight: 600;
            font-family: 'Space Grotesk', sans-serif;
            cursor: pointer;
            transition: all 0.25s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 0 18px rgba(124,58,237,0.4);
        }
        .btn-primary:hover { box-shadow: 0 0 30px rgba(124,58,237,0.7); transform: translateY(-1px); }
        .btn-gold {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: #1a0a00;
            border-radius: 12px;
            padding: 8px 20px;
            font-size: 13px;
            font-weight: 700;
            font-family: 'Space Grotesk', sans-serif;
            cursor: pointer;
            transition: all 0.25s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            box-shadow: 0 0 16px rgba(245,158,11,0.35);
        }
        .btn-gold:hover { box-shadow: 0 0 28px rgba(245,158,11,0.6); transform: translateY(-1px); }

        /* ── Main content area ─────────────────────────── */
        main {
            flex: 1;
            position: relative;
            z-index: 10;
        }

        /* ── Flash messages ────────────────────────────── */
        .flash-success {
            background: rgba(16,185,129,0.12);
            border: 1px solid rgba(16,185,129,0.3);
            color: #6ee7b7;
            border-radius: 14px;
            padding: 12px 18px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }
        .flash-error {
            background: rgba(239,68,68,0.12);
            border: 1px solid rgba(239,68,68,0.3);
            color: #fca5a5;
            border-radius: 14px;
            padding: 12px 18px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        /* ── Footer ────────────────────────────────────── */
        .glass-footer {
            background: rgba(6,0,15,0.55);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-top: 1px solid rgba(255,255,255,0.06);
            position: relative;
            z-index: 10;
        }

        /* ── Scrollbar ─────────────────────────────────── */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: rgba(255,255,255,0.02); }
        ::-webkit-scrollbar-thumb { background: rgba(139,92,246,0.4); border-radius: 4px; }
    </style>
    @stack('styles')
</head>
<body>

    {{-- ── NAV ─────────────────────────────────────────── --}}
    <nav class="glass-nav">
        <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between gap-4">

            <a href="{{ route('home') }}" class="flex items-center gap-2.5 shrink-0">
                <span class="text-2xl leading-none">🎟</span>
                <span class="text-white font-bold text-lg tracking-tight">
                    Reco<span class="text-gradient">Ticket</span>
                </span>
            </a>

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

            <div class="flex items-center gap-3 shrink-0">
                @guest
                    <a href="{{ route('login') }}" class="btn-ghost">Iniciar sesión</a>
                    <a href="{{ route('register') }}" class="btn-gold">Registrarse</a>
                @endguest
                @auth
                    <span class="text-sm text-white/45 hidden sm:block">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn-ghost">Salir</button>
                    </form>
                @endauth
            </div>
        </div>
    </nav>

    {{-- ── MAIN ─────────────────────────────────────────── --}}
    <main class="max-w-7xl mx-auto w-full px-6 py-8">
        @if(session('success'))
            <div class="flash-success">
                <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="10"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="flash-error">
                <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6"/></svg>
                {{ session('error') }}
            </div>
        @endif
        @yield('content')
    </main>

    {{-- ── FOOTER ───────────────────────────────────────── --}}
    <footer class="glass-footer">
        <div class="max-w-7xl mx-auto px-6 py-5 flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <span class="text-lg">🎟</span>
                <span class="font-bold text-white text-sm">Reco<span class="text-gradient">Ticket</span></span>
            </div>
            <p class="text-white/30 text-xs">© {{ date('Y') }} RecoTicket — Argentina</p>
            <div class="flex items-center gap-4">
                <a href="{{ route('home') }}" class="text-white/35 text-xs hover:text-white/65 transition">Eventos</a>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
