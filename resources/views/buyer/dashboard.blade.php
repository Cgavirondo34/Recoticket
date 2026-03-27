@extends('layouts.app')

@section('title', 'Mis Entradas')

@section('content')
<h1 class="text-2xl font-bold text-gray-800 mb-6">Mis próximas entradas</h1>

@if($tickets->isEmpty())
    <div class="text-center py-16 text-gray-400">
        <div class="text-5xl mb-4">🎟</div>
        <p class="text-lg">No tenés entradas para próximos eventos.</p>
        <a href="{{ route('home') }}" class="mt-4 inline-block bg-indigo-600 text-white px-6 py-2.5 rounded-lg font-medium hover:bg-indigo-700 transition">Ver eventos</a>
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($tickets as $ticket)
        <a href="{{ route('buyer.tickets.show', $ticket) }}" class="bg-white rounded-xl shadow hover:shadow-md transition p-5 block">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <h3 class="font-semibold text-gray-800">{{ $ticket->event?->title }}</h3>
                    <p class="text-indigo-600 text-sm">{{ $ticket->ticketType?->name }}</p>
                </div>
                <span class="text-xs px-2 py-1 rounded-full font-medium
                    {{ $ticket->status === 'valid' ? 'bg-green-100 text-green-700' : '' }}
                    {{ $ticket->status === 'used' ? 'bg-gray-100 text-gray-500' : '' }}
                    {{ $ticket->status === 'cancelled' ? 'bg-red-100 text-red-600' : '' }}">
                    {{ ucfirst($ticket->status) }}
                </span>
            </div>
            <p class="text-gray-400 text-xs">📅 {{ $ticket->event?->start_date?->format('d/m/Y H:i') }}</p>
            <p class="text-gray-400 text-xs font-mono mt-1">{{ substr($ticket->ticket_code, 0, 16) }}…</p>
        </a>
        @endforeach
    </div>

    <div class="mt-4 text-center">
        <a href="{{ route('buyer.tickets.index') }}" class="text-indigo-600 text-sm hover:underline">Ver todas mis entradas →</a>
    </div>
@endif
@endsection
