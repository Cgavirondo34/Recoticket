@extends('layouts.app')

@section('title', 'Panel Admin')

@section('content')
<h1 class="text-2xl font-bold text-gray-800 mb-8">Panel de Administración</h1>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    <div class="bg-white rounded-xl shadow p-6 text-center border-t-4 border-indigo-500">
        <div class="text-4xl font-bold text-indigo-600">{{ $stats['users'] }}</div>
        <div class="text-gray-500 text-sm mt-1">Usuarios</div>
    </div>
    <div class="bg-white rounded-xl shadow p-6 text-center border-t-4 border-purple-500">
        <div class="text-4xl font-bold text-purple-600">{{ $stats['events'] }}</div>
        <div class="text-gray-500 text-sm mt-1">Eventos</div>
    </div>
    <div class="bg-white rounded-xl shadow p-6 text-center border-t-4 border-green-500">
        <div class="text-4xl font-bold text-green-600">{{ $stats['tickets'] }}</div>
        <div class="text-gray-500 text-sm mt-1">Entradas vendidas</div>
    </div>
    <div class="bg-white rounded-xl shadow p-6 text-center border-t-4 border-yellow-500">
        <div class="text-4xl font-bold text-yellow-600">${{ number_format($stats['revenue'], 0, ',', '.') }}</div>
        <div class="text-gray-500 text-sm mt-1">Ingresos totales</div>
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <a href="{{ route('admin.users.index') }}" class="bg-white rounded-xl shadow hover:shadow-md transition p-5 flex items-center gap-4">
        <span class="text-3xl">👥</span>
        <div>
            <div class="font-semibold text-gray-700">Gestionar Usuarios</div>
            <div class="text-gray-400 text-sm">Roles y permisos</div>
        </div>
    </a>
    <a href="{{ route('admin.organizers.index') }}" class="bg-white rounded-xl shadow hover:shadow-md transition p-5 flex items-center gap-4">
        <span class="text-3xl">🎪</span>
        <div>
            <div class="font-semibold text-gray-700">Organizadores</div>
            <div class="text-gray-400 text-sm">Verificar cuentas</div>
        </div>
    </a>
    <a href="{{ route('admin.events.index') }}" class="bg-white rounded-xl shadow hover:shadow-md transition p-5 flex items-center gap-4">
        <span class="text-3xl">📅</span>
        <div>
            <div class="font-semibold text-gray-700">Eventos</div>
            <div class="text-gray-400 text-sm">Publicar / despublicar</div>
        </div>
    </a>
</div>
@endsection
