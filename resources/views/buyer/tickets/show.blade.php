@extends('layouts.app')

@section('title', 'Entrada — ' . $ticket->ticket_code)

@section('content')
<div class="max-w-md mx-auto">
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        {{-- Header --}}
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white p-6 text-center">
            <p class="text-indigo-200 text-sm mb-1">{{ $ticket->event?->category?->name }}</p>
            <h1 class="text-xl font-bold">{{ $ticket->event?->title }}</h1>
            <p class="text-indigo-200 text-sm mt-1">{{ $ticket->event?->start_date?->format('d \d\e F \d\e Y — H:i \h\s') }}</p>
            @if($ticket->event?->venue)
                <p class="text-indigo-200 text-xs mt-1">📍 {{ $ticket->event->venue->name }}, {{ $ticket->event->venue->city }}</p>
            @endif
        </div>

        {{-- QR --}}
        <div class="p-6 text-center border-b border-dashed border-gray-200">
            @if($ticket->qr_code_path && $ticket->status === 'valid')
                <img src="{{ asset('storage/' . $ticket->qr_code_path) }}" alt="QR Code" class="mx-auto w-48 h-48">
            @elseif($ticket->status === 'used')
                <div class="w-48 h-48 mx-auto flex items-center justify-center bg-gray-100 rounded-xl">
                    <div class="text-center text-gray-400">
                        <div class="text-4xl mb-2">✅</div>
                        <div class="text-sm font-medium">Utilizado</div>
                        @if($ticket->checked_in_at)
                            <div class="text-xs mt-1">{{ $ticket->checked_in_at->format('d/m/Y H:i') }}</div>
                        @endif
                    </div>
                </div>
            @else
                <div class="w-48 h-48 mx-auto flex items-center justify-center bg-gray-100 rounded-xl">
                    <div class="text-center text-gray-400">
                        <div class="text-4xl mb-2">❌</div>
                        <div class="text-sm">{{ ucfirst($ticket->status) }}</div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Info --}}
        <div class="p-6 space-y-3">
            <div class="flex justify-between text-sm">
                <span class="text-gray-400">Tipo de entrada</span>
                <span class="font-semibold text-gray-700">{{ $ticket->ticketType?->name }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-400">Estado</span>
                <span class="font-semibold
                    {{ $ticket->status === 'valid' ? 'text-green-600' : '' }}
                    {{ $ticket->status === 'used' ? 'text-gray-500' : '' }}
                    {{ $ticket->status === 'cancelled' ? 'text-red-500' : '' }}">
                    {{ ucfirst($ticket->status) }}
                </span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-400">Orden</span>
                <span class="font-mono text-xs text-gray-600">{{ $ticket->order?->order_number }}</span>
            </div>
            <div class="bg-gray-50 rounded-lg px-3 py-2 text-center">
                <p class="text-xs text-gray-400 mb-1">Código</p>
                <p class="font-mono text-xs text-gray-700 break-all">{{ $ticket->ticket_code }}</p>
            </div>
        </div>
    </div>

    <a href="{{ route('buyer.tickets.index') }}" class="block text-center text-sm text-gray-400 mt-4 hover:text-gray-600">← Volver a mis entradas</a>
</div>
@endsection
