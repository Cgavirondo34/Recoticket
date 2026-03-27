@extends('layouts.app')

@section('title', 'Editar Tipo de Entrada')

@section('content')
<div class="max-w-lg mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-2">Editar tipo de entrada</h1>
    <p class="text-gray-500 mb-6 text-sm">Evento: <strong>{{ $event->title }}</strong></p>

    @if($errors->any())
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            @foreach($errors->all() as $e)<p class="text-sm">{{ $e }}</p>@endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('organizer.ticket-types.update', $ticketType) }}" class="bg-white rounded-xl shadow p-6 space-y-4">
        @csrf @method('PUT')
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
            <input type="text" name="name" value="{{ old('name', $ticketType->name) }}" required
                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
            <textarea name="description" rows="2"
                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('description', $ticketType->description) }}</textarea>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Precio (ARS) *</label>
                <input type="number" name="price" value="{{ old('price', $ticketType->price) }}" min="0" step="0.01" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cantidad *</label>
                <input type="number" name="quantity" value="{{ old('quantity', $ticketType->quantity) }}" min="1" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Inicio de venta</label>
                <input type="datetime-local" name="sale_start" value="{{ old('sale_start', $ticketType->sale_start?->format('Y-m-d\TH:i')) }}"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fin de venta</label>
                <input type="datetime-local" name="sale_end" value="{{ old('sale_end', $ticketType->sale_end?->format('Y-m-d\TH:i')) }}"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
            <select name="status" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="active" {{ old('status', $ticketType->status) === 'active' ? 'selected' : '' }}>Activo</option>
                <option value="inactive" {{ old('status', $ticketType->status) === 'inactive' ? 'selected' : '' }}>Inactivo</option>
            </select>
        </div>
        <div class="flex gap-3">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg font-medium transition">Guardar</button>
            <a href="{{ route('organizer.events.ticket-types.index', $event) }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2.5 rounded-lg font-medium transition">Cancelar</a>
        </div>
    </form>
</div>
@endsection
