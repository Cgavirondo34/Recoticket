@extends('layouts.app')

@section('title', $event->title)

@section('content')
<div class="max-w-4xl mx-auto">
    {{-- Hero --}}
    <div class="relative rounded-2xl overflow-hidden mb-8 shadow-lg">
        @if($event->cover_image)
            <img src="{{ $event->cover_image }}" alt="{{ $event->title }}" class="w-full h-72 object-cover">
        @else
            <div class="w-full h-72 bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-8xl">🎵</div>
        @endif
        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
        <div class="absolute bottom-0 left-0 p-6 text-white">
            @if($event->category)
                <span class="inline-block bg-indigo-500/80 text-xs font-medium px-3 py-1 rounded-full mb-2">{{ $event->category->name }}</span>
            @endif
            <h1 class="text-3xl font-bold leading-tight">{{ $event->title }}</h1>
            @if($event->organizer)
                <p class="text-indigo-200 text-sm mt-1">Por {{ $event->organizer->name }}</p>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Details --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Sobre el evento</h2>
                <div class="text-gray-600 leading-relaxed">
                    {!! nl2br(e($event->description ?? 'Sin descripción disponible.')) !!}
                </div>
            </div>

            @if($event->venue)
            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-3">📍 Lugar</h2>
                <p class="font-semibold text-gray-700">{{ $event->venue->name }}</p>
                <p class="text-gray-500 text-sm">{{ $event->venue->address }}</p>
                <p class="text-gray-500 text-sm">{{ $event->venue->city }}, {{ $event->venue->state }}, {{ $event->venue->country }}</p>
            </div>
            @endif
        </div>

        {{-- Ticket sidebar --}}
        <div class="space-y-4">
            <div class="bg-white rounded-xl shadow p-6">
                <div class="text-sm text-indigo-600 font-semibold mb-1">📅 Fecha</div>
                <div class="font-bold text-gray-700">{{ $event->start_date?->format('d \d\e F \d\e Y') }}</div>
                <div class="text-gray-500 text-sm">{{ $event->start_date?->format('H:i') }} hs</div>
                @if($event->end_date)
                    <div class="text-gray-400 text-xs mt-1">Hasta {{ $event->end_date?->format('H:i') }} hs</div>
                @endif
            </div>

            @if($event->ticketTypes->isNotEmpty())
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="font-bold text-gray-800 mb-4">🎟 Entradas disponibles</h3>
                @foreach($event->ticketTypes as $type)
                <div class="flex justify-between items-center border-b border-gray-100 pb-3 mb-3 last:border-0 last:pb-0 last:mb-0">
                    <div>
                        <div class="font-semibold text-gray-700 text-sm">{{ $type->name }}</div>
                        @if($type->description)
                            <div class="text-gray-400 text-xs">{{ $type->description }}</div>
                        @endif
                        <div class="text-xs text-gray-400 mt-1">{{ $type->available }} disponibles</div>
                    </div>
                    <div class="text-right">
                        @if($type->price == 0)
                            <span class="text-green-600 font-bold text-sm">Gratis</span>
                        @else
                            <span class="text-indigo-700 font-bold text-sm">${{ number_format($type->price, 0, ',', '.') }}</span>
                        @endif
                    </div>
                </div>
                @endforeach
                @auth
                    <a href="{{ route('checkout.show', $event->slug) }}"
                        class="block w-full mt-4 bg-indigo-600 hover:bg-indigo-700 text-white text-center py-3 rounded-xl font-semibold transition">
                        Comprar entradas
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="block w-full mt-4 bg-gray-200 hover:bg-gray-300 text-gray-700 text-center py-3 rounded-xl font-semibold transition">
                        Iniciá sesión para comprar
                    </a>
                @endauth
            </div>
            @else
                <div class="bg-gray-100 rounded-xl p-6 text-center text-gray-500 text-sm">
                    No hay entradas disponibles en este momento.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
