@extends('layouts.app')

@section('title', 'Tipos de Entrada')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Tipos de Entrada</h1>
        <p class="text-gray-500 text-sm">Evento: <strong>{{ $event->title }}</strong></p>
    </div>
    <a href="{{ route('organizer.events.ticket-types.create', $event) }}"
        class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg font-medium transition text-sm">
        + Agregar tipo
    </a>
</div>

@if($ticketTypes->isEmpty())
    <div class="text-center py-12 text-gray-400 bg-white rounded-xl shadow">
        <div class="text-4xl mb-3">🎟</div>
        <p>No hay tipos de entrada para este evento.</p>
    </div>
@else
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-400 uppercase text-xs">
                <tr>
                    <th class="px-5 py-3 text-left">Nombre</th>
                    <th class="px-5 py-3 text-right">Precio</th>
                    <th class="px-5 py-3 text-center">Cantidad</th>
                    <th class="px-5 py-3 text-center">Vendidas</th>
                    <th class="px-5 py-3 text-center">Disponibles</th>
                    <th class="px-5 py-3 text-left">Estado</th>
                    <th class="px-5 py-3">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($ticketTypes as $type)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3 font-medium text-gray-700">
                        {{ $type->name }}
                        @if($type->description)
                            <p class="text-gray-400 text-xs">{{ $type->description }}</p>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-right font-semibold text-indigo-600">
                        @if($type->price == 0) Gratis @else ${{ number_format($type->price, 0, ',', '.') }} @endif
                    </td>
                    <td class="px-5 py-3 text-center">{{ $type->quantity }}</td>
                    <td class="px-5 py-3 text-center text-green-600 font-medium">{{ $type->quantity_sold }}</td>
                    <td class="px-5 py-3 text-center text-orange-500 font-medium">{{ $type->available }}</td>
                    <td class="px-5 py-3">
                        <span class="text-xs px-2 py-0.5 rounded-full
                            {{ $type->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $type->status }}
                        </span>
                    </td>
                    <td class="px-5 py-3 flex gap-2">
                        <a href="{{ route('organizer.ticket-types.edit', $type) }}" class="text-indigo-600 text-xs hover:underline">Editar</a>
                        <form method="POST" action="{{ route('organizer.ticket-types.destroy', $type) }}" onsubmit="return confirm('¿Eliminar?')">
                            @csrf @method('DELETE')
                            <button class="text-red-500 text-xs hover:underline">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

<a href="{{ route('organizer.events.index') }}" class="block text-sm text-gray-400 mt-4 hover:text-gray-600">← Volver a mis eventos</a>
@endsection
