<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Gestión') — GymField Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f1f5f9; color: #1e293b; min-height: 100vh; }
        .sidebar { width: 240px; min-height: 100vh; background: #1e293b; position: fixed; top: 0; left: 0; z-index: 40; }
        .sidebar-link { display: flex; align-items: center; gap: 10px; padding: 10px 16px; color: #94a3b8; font-size: 14px; font-weight: 500; border-radius: 8px; margin: 2px 8px; transition: all 0.15s; text-decoration: none; }
        .sidebar-link:hover, .sidebar-link.active { background: rgba(255,255,255,0.08); color: #f1f5f9; }
        .sidebar-section { font-size: 11px; font-weight: 600; color: #475569; text-transform: uppercase; letter-spacing: 0.08em; padding: 16px 24px 4px; }
        .main-content { margin-left: 240px; min-height: 100vh; }
        .topbar { background: #fff; border-bottom: 1px solid #e2e8f0; padding: 0 24px; height: 56px; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 30; }
        .card { background: #fff; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); }
        .badge-active { background: #dcfce7; color: #166534; font-size: 12px; padding: 2px 8px; border-radius: 20px; font-weight: 500; }
        .badge-expired { background: #fee2e2; color: #991b1b; font-size: 12px; padding: 2px 8px; border-radius: 20px; font-weight: 500; }
        .badge-suspended { background: #fef9c3; color: #854d0e; font-size: 12px; padding: 2px 8px; border-radius: 20px; font-weight: 500; }
        .badge-paid { background: #dcfce7; color: #166534; font-size: 12px; padding: 2px 8px; border-radius: 20px; font-weight: 500; }
        .badge-pending { background: #fef9c3; color: #854d0e; font-size: 12px; padding: 2px 8px; border-radius: 20px; font-weight: 500; }
        .badge-overdue { background: #fee2e2; color: #991b1b; font-size: 12px; padding: 2px 8px; border-radius: 20px; font-weight: 500; }
        .btn-primary { background: #3b82f6; color: #fff; padding: 8px 16px; border-radius: 8px; font-size: 14px; font-weight: 500; border: none; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; transition: background 0.15s; }
        .btn-primary:hover { background: #2563eb; }
        .btn-secondary { background: #f1f5f9; color: #475569; padding: 8px 16px; border-radius: 8px; font-size: 14px; font-weight: 500; border: 1px solid #e2e8f0; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; transition: all 0.15s; }
        .btn-secondary:hover { background: #e2e8f0; }
        .btn-danger { background: #ef4444; color: #fff; padding: 8px 16px; border-radius: 8px; font-size: 14px; font-weight: 500; border: none; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
        .btn-danger:hover { background: #dc2626; }
        .form-input { width: 100%; border: 1px solid #e2e8f0; border-radius: 8px; padding: 8px 12px; font-size: 14px; color: #1e293b; outline: none; transition: border 0.15s; }
        .form-input:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
        .form-label { font-size: 13px; font-weight: 500; color: #374151; margin-bottom: 4px; display: block; }
        .stat-card { background: #fff; border-radius: 12px; padding: 20px; border-left: 4px solid; }
        .table-row:hover { background: #f8fafc; }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); transition: transform 0.3s; }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>
    {{-- ── SIDEBAR ──────────────────────────────────────────────────────────── --}}
    <aside class="sidebar" id="sidebar">
        <div class="p-4 border-b border-slate-700">
            <a href="{{ route('gym.dashboard') }}" class="flex items-center gap-3">
                <span class="text-2xl">🏋️</span>
                <div>
                    <div class="text-white font-bold text-sm">GymField</div>
                    <div class="text-slate-400 text-xs">Manager</div>
                </div>
            </a>
        </div>

        <nav class="py-4 overflow-y-auto">
            <div class="sidebar-section">Principal</div>
            <a href="{{ route('gym.dashboard') }}" class="sidebar-link {{ request()->routeIs('gym.dashboard') ? 'active' : '' }}">
                📊 Dashboard
            </a>

            <div class="sidebar-section">Gimnasio</div>
            <a href="{{ route('gym.members.index') }}" class="sidebar-link {{ request()->routeIs('gym.members.*') ? 'active' : '' }}">
                👥 Socios
            </a>
            <a href="{{ route('gym.plans.index') }}" class="sidebar-link {{ request()->routeIs('gym.plans.*') ? 'active' : '' }}">
                📋 Planes
            </a>
            <a href="{{ route('gym.payments.index') }}" class="sidebar-link {{ request()->routeIs('gym.payments.*') ? 'active' : '' }}">
                💰 Pagos
            </a>
            <a href="{{ route('gym.routines.index') }}" class="sidebar-link {{ request()->routeIs('gym.routines.*') ? 'active' : '' }}">
                🏃 Rutinas
            </a>

            <div class="sidebar-section">Cancha</div>
            <a href="{{ route('gym.reservations.index') }}" class="sidebar-link {{ request()->routeIs('gym.reservations.*') ? 'active' : '' }}">
                🗓️ Reservas
            </a>

            <div class="sidebar-section">Finanzas</div>
            <a href="{{ route('gym.expenses.index') }}" class="sidebar-link {{ request()->routeIs('gym.expenses.*') ? 'active' : '' }}">
                📉 Gastos
            </a>
            <a href="{{ route('gym.settlement.index') }}" class="sidebar-link {{ request()->routeIs('gym.settlement.*') ? 'active' : '' }}">
                📊 Liquidación
            </a>

            <div class="sidebar-section">Sistema</div>
            <a href="{{ route('gym.settings.index') }}" class="sidebar-link {{ request()->routeIs('gym.settings.*') ? 'active' : '' }}">
                ⚙️ Configuración
            </a>
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link">
                🎟️ RecoTicket
            </a>
        </nav>
    </aside>

    {{-- ── MAIN ──────────────────────────────────────────────────────────────── --}}
    <div class="main-content">
        {{-- Topbar --}}
        <div class="topbar">
            <div class="flex items-center gap-3">
                <button onclick="document.getElementById('sidebar').classList.toggle('open')" class="md:hidden text-slate-500 hover:text-slate-700">
                    ☰
                </button>
                <h1 class="font-semibold text-slate-800 text-sm">@yield('title', 'Panel')</h1>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-sm text-slate-500">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-slate-400 hover:text-slate-600">Salir</button>
                </form>
            </div>
        </div>

        {{-- Content --}}
        <main class="p-6">
            @if(session('success'))
                <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm flex items-center gap-2">
                    ✅ {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm flex items-center gap-2">
                    ❌ {{ session('error') }}
                </div>
            @endif
            @if($errors->any())
                <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
