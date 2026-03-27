@extends('layouts.app')

@section('title', 'Panel Organizador')

@section('content')
<h1 class="text-2xl font-bold text-gray-800 mb-6">Panel del Organizador</h1>
<p class="text-gray-500 mb-8">Bienvenido, <strong>{{ $organizer->name }}</strong>
    @if($organizer->verified)<span class="inline-block bg-blue-100 text-blue-600 text-xs px-2 py-0.5 rounded-full ml-2">✓ Verificado</span>@endif
</p>

{{-- Stats --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
    <div class="bg-white rounded-xl shadow p-5 text-center">
        <div class="text-3xl font-bold text-indigo-600">{{ $events->count() }}</div>
        <div class="text-gray-500 text-sm mt-1">Eventos</div>
    </div>
    <div class="bg-white rounded-xl shadow p-5 text-center">
        <div class="text-3xl font-bold text-green-600">{{ $totalTickets }}</div>
        <div class="text-gray-500 text-sm mt-1">Entradas vendidas</div>
    </div>
    <div class="bg-white rounded-xl shadow p-5 text-center">
        <div class="text-3xl font-bold text-yellow-600">${{ number_format($totalRevenue, 0, ',', '.') }}</div>
        <div class="text-gray-500 text-sm mt-1">Ingresos totales</div>
    </div>
</div>

{{-- Quick links --}}
<div class="flex flex-wrap gap-3 mb-8">
    <a href="{{ route('organizer.events.index') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg font-medium transition text-sm">Ver mis eventos</a>
    <a href="{{ route('organizer.events.create') }}" class="bg-white border border-indigo-300 hover:border-indigo-500 text-indigo-600 px-5 py-2.5 rounded-lg font-medium transition text-sm">+ Nuevo evento</a>
    <a href="{{ route('organizer.scan') }}" class="bg-white border border-green-300 hover:border-green-500 text-green-600 px-5 py-2.5 rounded-lg font-medium transition text-sm">📷 Escanear entrada</a>
</div>

{{-- Events table --}}
@if($events->isNotEmpty())
<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-400 uppercase text-xs">
            <tr>
                <th class="px-5 py-3 text-left">Evento</th>
                <th class="px-5 py-3 text-left">Fecha</th>
                <th class="px-5 py-3 text-center">Entradas</th>
                <th class="px-5 py-3 text-left">Estado</th>
                <th class="px-5 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($events as $event)
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-3 font-medium text-gray-700">{{ $event->title }}</td>
                <td class="px-5 py-3 text-gray-500">{{ $event->start_date?->format('d/m/Y') }}</td>
                <td class="px-5 py-3 text-center font-semibold text-indigo-600">{{ $event->tickets_count }}</td>
                <td class="px-5 py-3">
                    <span class="text-xs px-2 py-0.5 rounded-full
                        {{ $event->status === 'published' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ $event->status }}
                    </span>
                </td>
                <td class="px-5 py-3">
                    <a href="{{ route('organizer.events.edit', $event) }}" class="text-indigo-600 text-xs hover:underline">Editar</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif
@endsection
