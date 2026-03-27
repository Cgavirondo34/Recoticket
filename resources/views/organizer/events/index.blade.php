@extends('layouts.app')

@section('title', 'Mis Eventos')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Mis Eventos</h1>
    <a href="{{ route('organizer.events.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg font-medium transition text-sm">+ Nuevo evento</a>
</div>

@if($events->isEmpty())
    <div class="text-center py-16 text-gray-400">
        <div class="text-5xl mb-4">🎪</div>
        <p class="text-lg mb-4">No tenés eventos aún.</p>
        <a href="{{ route('organizer.events.create') }}" class="bg-indigo-600 text-white px-6 py-2.5 rounded-lg font-medium hover:bg-indigo-700 transition">Crear mi primer evento</a>
    </div>
@else
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-400 uppercase text-xs">
                <tr>
                    <th class="px-5 py-3 text-left">Título</th>
                    <th class="px-5 py-3 text-left">Categoría</th>
                    <th class="px-5 py-3 text-left">Lugar</th>
                    <th class="px-5 py-3 text-left">Fecha inicio</th>
                    <th class="px-5 py-3 text-left">Estado</th>
                    <th class="px-5 py-3">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($events as $event)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3 font-medium text-gray-700">{{ $event->title }}</td>
                    <td class="px-5 py-3 text-gray-500">{{ $event->category?->name }}</td>
                    <td class="px-5 py-3 text-gray-500">{{ $event->venue?->city ?? '—' }}</td>
                    <td class="px-5 py-3 text-gray-500">{{ $event->start_date?->format('d/m/Y') }}</td>
                    <td class="px-5 py-3">
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium
                            {{ $event->status === 'published' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $event->status === 'draft' ? 'bg-gray-100 text-gray-500' : '' }}
                            {{ $event->status === 'cancelled' ? 'bg-red-100 text-red-600' : '' }}">
                            {{ $event->status }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-center flex gap-2 justify-center">
                        <a href="{{ route('organizer.events.ticket-types.index', $event) }}" class="text-green-600 text-xs hover:underline">Entradas</a>
                        <a href="{{ route('organizer.events.edit', $event) }}" class="text-indigo-600 text-xs hover:underline">Editar</a>
                        <form method="POST" action="{{ route('organizer.events.destroy', $event) }}" onsubmit="return confirm('¿Eliminar este evento?')">
                            @csrf @method('DELETE')
                            <button class="text-red-500 text-xs hover:underline">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $events->links() }}</div>
@endif
@endsection
