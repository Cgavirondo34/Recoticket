@extends('layouts.app')

@section('title', 'Mis Entradas')

@section('content')
<h1 class="text-2xl font-bold text-gray-800 mb-6">Mis Entradas</h1>

@if($tickets->isEmpty())
    <div class="text-center py-16 text-gray-400">
        <div class="text-5xl mb-4">🎟</div>
        <p class="text-lg">No tenés entradas aún.</p>
        <a href="{{ route('home') }}" class="mt-4 inline-block bg-indigo-600 text-white px-6 py-2.5 rounded-lg font-medium hover:bg-indigo-700 transition">Ver eventos</a>
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($tickets as $ticket)
        <a href="{{ route('buyer.tickets.show', $ticket) }}"
            class="bg-white rounded-xl shadow hover:shadow-md transition p-5 block border-l-4
            {{ $ticket->status === 'valid' ? 'border-green-400' : '' }}
            {{ $ticket->status === 'used' ? 'border-gray-300' : '' }}
            {{ $ticket->status === 'cancelled' ? 'border-red-400' : '' }}">
            <div class="flex items-start justify-between mb-2">
                <h3 class="font-semibold text-gray-800 text-sm">{{ $ticket->event?->title }}</h3>
                <span class="text-xs px-2 py-0.5 rounded-full font-medium
                    {{ $ticket->status === 'valid' ? 'bg-green-100 text-green-700' : '' }}
                    {{ $ticket->status === 'used' ? 'bg-gray-100 text-gray-500' : '' }}
                    {{ $ticket->status === 'cancelled' ? 'bg-red-100 text-red-600' : '' }}">
                    {{ ucfirst($ticket->status) }}
                </span>
            </div>
            <p class="text-indigo-600 text-xs font-medium mb-2">{{ $ticket->ticketType?->name }}</p>
            <p class="text-gray-400 text-xs">📅 {{ $ticket->event?->start_date?->format('d/m/Y H:i') }}</p>
            <p class="text-gray-300 text-xs font-mono mt-1">{{ substr($ticket->ticket_code, 0, 20) }}…</p>
        </a>
        @endforeach
    </div>
    <div class="mt-6">{{ $tickets->links() }}</div>
@endif
@endsection
