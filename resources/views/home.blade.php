@extends('layouts.app')

@section('title', 'Eventos')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-2">Próximos Eventos</h1>
    <p class="text-gray-500">Encontrá las mejores experiencias en Argentina</p>
</div>

{{-- Filters --}}
<form method="GET" action="{{ route('home') }}" class="mb-8 flex flex-wrap gap-3">
    <input type="text" name="search" value="{{ request('search') }}"
        placeholder="Buscar eventos..."
        class="border border-gray-300 rounded-lg px-4 py-2 w-64 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    <select name="category" class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <option value="">Todas las categorías</option>
        @foreach($categories as $cat)
            <option value="{{ $cat->slug }}" @selected(request('category') === $cat->slug)>{{ $cat->name }}</option>
        @endforeach
    </select>
    <button type="submit" class="bg-indigo-600 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition">Buscar</button>
    @if(request()->hasAny(['search', 'category']))
        <a href="{{ route('home') }}" class="text-sm text-gray-500 self-center hover:text-gray-700">Limpiar</a>
    @endif
</form>

@if($events->isEmpty())
    <div class="text-center py-16 text-gray-400">
        <div class="text-5xl mb-4">🎟</div>
        <p class="text-xl">No se encontraron eventos.</p>
    </div>
@else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach($events as $event)
        <a href="{{ route('events.show', $event->slug) }}" class="bg-white rounded-xl shadow hover:shadow-lg transition overflow-hidden group">
            @if($event->cover_image)
                <img src="{{ $event->cover_image }}" alt="{{ $event->title }}" class="w-full h-44 object-cover group-hover:scale-105 transition duration-300">
            @else
                <div class="w-full h-44 bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-5xl">🎵</div>
            @endif
            <div class="p-4">
                @if($event->featured)
                    <span class="inline-block bg-yellow-100 text-yellow-700 text-xs font-semibold px-2 py-0.5 rounded-full mb-2">⭐ Destacado</span>
                @endif
                <h2 class="font-bold text-gray-800 text-sm mb-1 line-clamp-2">{{ $event->title }}</h2>
                <p class="text-indigo-600 text-xs font-medium mb-1">
                    {{ $event->start_date?->format('d M Y, H:i') }}
                </p>
                @if($event->venue)
                    <p class="text-gray-400 text-xs mb-2">📍 {{ $event->venue->city }}, {{ $event->venue->state }}</p>
                @endif
                @if($event->category)
                    <span class="inline-block bg-indigo-100 text-indigo-600 text-xs px-2 py-0.5 rounded-full">{{ $event->category->name }}</span>
                @endif
                @php
                    $minPrice = $event->ticketTypes->min('price');
                @endphp
                @if($minPrice !== null)
                    <div class="mt-3 text-sm font-bold text-gray-700">
                        @if($minPrice == 0)
                            <span class="text-green-600">Gratis</span>
                        @else
                            Desde ${{ number_format($minPrice, 0, ',', '.') }}
                        @endif
                    </div>
                @endif
            </div>
        </a>
        @endforeach
    </div>
    <div class="mt-8">{{ $events->links() }}</div>
@endif
@endsection
