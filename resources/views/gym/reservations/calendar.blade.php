@extends('layouts.gym')

@section('title', 'Reservas — Cancha')

@section('content')
<div class="flex items-center justify-between mb-6 flex-wrap gap-3">
    <div class="flex items-center gap-3">
        <h1 class="text-xl font-bold text-slate-800">Reservas</h1>
        <div class="flex gap-1 border rounded-lg p-1 bg-white">
            <a href="{{ route('gym.reservations.index', ['view' => 'daily', 'date' => $date->toDateString()]) }}"
               class="px-3 py-1 rounded text-xs font-medium transition {{ $view === 'daily' ? 'bg-blue-600 text-white' : 'text-slate-500 hover:bg-slate-50' }}">
                Día
            </a>
            <a href="{{ route('gym.reservations.index', ['view' => 'weekly', 'date' => $date->toDateString()]) }}"
               class="px-3 py-1 rounded text-xs font-medium transition {{ $view === 'weekly' ? 'bg-blue-600 text-white' : 'text-slate-500 hover:bg-slate-50' }}">
                Semana
            </a>
        </div>
    </div>
    <div class="flex items-center gap-3">
        {{-- Date navigator --}}
        @if($view === 'weekly')
            @php $prev = $weekStart->copy()->subWeek(); $next = $weekStart->copy()->addWeek(); @endphp
            <a href="{{ route('gym.reservations.index', ['view' => 'weekly', 'date' => $prev->toDateString()]) }}" class="btn-secondary">←</a>
            <span class="text-sm font-medium text-slate-600">
                {{ $weekStart->format('d/m') }} — {{ $weekStart->copy()->endOfWeek()->format('d/m/Y') }}
            </span>
            <a href="{{ route('gym.reservations.index', ['view' => 'weekly', 'date' => $next->toDateString()]) }}" class="btn-secondary">→</a>
        @else
            @php $prev = $date->copy()->subDay(); $next = $date->copy()->addDay(); @endphp
            <a href="{{ route('gym.reservations.index', ['view' => 'daily', 'date' => $prev->toDateString()]) }}" class="btn-secondary">←</a>
            <span class="text-sm font-medium text-slate-600">{{ $date->isoFormat('dddd D [de] MMMM') }}</span>
            <a href="{{ route('gym.reservations.index', ['view' => 'daily', 'date' => $next->toDateString()]) }}" class="btn-secondary">→</a>
        @endif
        <a href="{{ route('gym.reservations.create', ['date' => $date->toDateString()]) }}" class="btn-primary">+ Nueva reserva</a>
    </div>
</div>

@if($view === 'weekly' && isset($calendar))
    {{-- Weekly calendar --}}
    <div class="card overflow-x-auto">
        <table class="w-full text-sm border-collapse min-w-[700px]">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-3 py-3 text-left text-slate-400 font-medium border-r w-28">Turno</th>
                    @foreach($calendar as $day => $_)
                        @php $d = \Carbon\Carbon::parse($day); @endphp
                        <th class="px-3 py-3 text-center font-medium border-r last:border-0 {{ $d->isToday() ? 'bg-blue-50 text-blue-700' : 'text-slate-600' }}">
                            <div>{{ $d->isoFormat('ddd') }}</div>
                            <div class="text-lg font-bold">{{ $d->format('d') }}</div>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($timeSlots as $slot)
                    <tr class="border-t">
                        <td class="px-3 py-3 text-xs text-slate-500 border-r font-medium whitespace-nowrap">
                            {{ $slot->label }}<br>
                            <span class="text-slate-400">{{ substr($slot->starts_at, 0, 5) }}–{{ substr($slot->ends_at, 0, 5) }}</span>
                        </td>
                        @foreach($calendar as $day => $dayReservations)
                            @php
                                $res = collect($dayReservations)->first(fn($r) => $r->field_time_slot_id === $slot->id);
                                $d = \Carbon\Carbon::parse($day);
                            @endphp
                            <td class="px-2 py-2 border-r last:border-0 align-top {{ $d->isToday() ? 'bg-blue-50' : '' }}">
                                @if($res)
                                    <a href="{{ route('gym.reservations.show', $res) }}"
                                       class="block p-2 rounded-lg text-xs {{ $res->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }} hover:opacity-80">
                                        <div class="font-semibold truncate">{{ $res->customer_name }}</div>
                                        <div class="mt-0.5 flex items-center gap-1">
                                            @if($res->type === 'recurring')
                                                <span title="Fija">🔄</span>
                                            @endif
                                            <span>{{ $res->payment_status === 'paid' ? '✅' : '💰' }}</span>
                                        </div>
                                    </a>
                                @else
                                    <a href="{{ route('gym.reservations.create', ['date' => $day, 'slot_id' => $slot->id]) }}"
                                       class="block text-center py-2 text-slate-300 hover:bg-slate-50 rounded text-xs">
                                        +
                                    </a>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    {{-- Daily view --}}
    <div class="space-y-3">
        @foreach($timeSlots as $slot)
            @php
                $res = isset($reservations)
                    ? collect($reservations)->first(fn($r) => $r->field_time_slot_id === $slot->id)
                    : null;
            @endphp
            <div class="card p-4 flex items-center justify-between gap-4">
                <div class="shrink-0 w-32">
                    <div class="font-semibold text-slate-700">{{ $slot->label }}</div>
                    <div class="text-xs text-slate-400">{{ substr($slot->starts_at, 0, 5) }}–{{ substr($slot->ends_at, 0, 5) }}</div>
                    <div class="text-xs text-slate-400">${{ number_format($slot->price, 0, ',', '.') }}</div>
                </div>
                @if($res)
                    <div class="flex-1">
                        <a href="{{ route('gym.reservations.show', $res) }}" class="font-medium text-slate-700 hover:text-blue-600">
                            {{ $res->customer_name }}
                        </a>
                        @if($res->customer_phone)
                            <span class="text-xs text-slate-400 ml-2">{{ $res->customer_phone }}</span>
                        @endif
                        @if($res->type === 'recurring')
                            <span class="ml-2 text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded">Fija</span>
                        @endif
                    </div>
                    <span class="badge-{{ $res->payment_status }}">
                        {{ $res->payment_status === 'paid' ? 'Pagado' : ($res->payment_status === 'partial' ? 'Parcial' : 'Pendiente') }}
                    </span>
                @else
                    <div class="flex-1 text-slate-300 text-sm">Disponible</div>
                    <a href="{{ route('gym.reservations.create', ['date' => $date->toDateString(), 'slot_id' => $slot->id]) }}" class="btn-secondary text-xs">
                        Reservar
                    </a>
                @endif
            </div>
        @endforeach

        @if(count($timeSlots) === 0)
            <div class="card p-8 text-center text-slate-400">
                No hay turnos configurados.
                <a href="{{ route('gym.settings.index') }}" class="text-blue-600 hover:underline ml-1">Configurar →</a>
            </div>
        @endif
    </div>
@endif
@endsection
