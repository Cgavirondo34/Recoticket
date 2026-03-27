<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'RecoTicket') — RecoTicket</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">

<nav class="bg-indigo-700 text-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <a href="{{ route('home') }}" class="flex items-center space-x-2 text-xl font-bold tracking-tight">
                <span class="text-yellow-300">🎟</span>
                <span>RecoTicket</span>
            </a>
            <div class="flex items-center space-x-4">
                <a href="{{ route('home') }}" class="hover:text-yellow-300 transition text-sm font-medium">Eventos</a>
                @guest
                    <a href="{{ route('login') }}" class="hover:text-yellow-300 transition text-sm font-medium">Iniciar sesión</a>
                    <a href="{{ route('register') }}" class="bg-yellow-400 text-indigo-900 px-4 py-1.5 rounded-full text-sm font-semibold hover:bg-yellow-300 transition">Registrarse</a>
                @endguest
                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="hover:text-yellow-300 transition text-sm font-medium">Admin</a>
                    @elseif(auth()->user()->isOrganizer())
                        <a href="{{ route('organizer.dashboard') }}" class="hover:text-yellow-300 transition text-sm font-medium">Panel Organizador</a>
                        <a href="{{ route('organizer.scan') }}" class="hover:text-yellow-300 transition text-sm font-medium">Escanear</a>
                    @else
                        <a href="{{ route('buyer.dashboard') }}" class="hover:text-yellow-300 transition text-sm font-medium">Mis Entradas</a>
                        <a href="{{ route('buyer.orders.index') }}" class="hover:text-yellow-300 transition text-sm font-medium">Mis Órdenes</a>
                    @endif
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-indigo-200">{{ auth()->user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="bg-indigo-600 border border-indigo-400 px-3 py-1 rounded text-xs hover:bg-indigo-500 transition">Salir</button>
                        </form>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</nav>

<main class="flex-1 max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8">
    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded">{{ session('error') }}</div>
    @endif
    @yield('content')
</main>

<footer class="bg-indigo-800 text-indigo-200 text-center py-4 text-sm">
    © {{ date('Y') }} RecoTicket — Argentina
</footer>

</body>
</html>
