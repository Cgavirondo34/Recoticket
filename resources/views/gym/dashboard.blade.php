@extends('layouts.gym')

@section('title', 'Dashboard')

@section('content')
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    {{-- Active Members --}}
    <div class="stat-card border-green-500">
        <div class="text-3xl font-bold text-green-600">{{ $stats['members_active'] }}</div>
        <div class="text-slate-500 text-sm mt-1">Socios activos</div>
    </div>
    {{-- Expired Members --}}
    <div class="stat-card border-red-500">
        <div class="text-3xl font-bold text-red-600">{{ $stats['members_expired'] }}</div>
        <div class="text-slate-500 text-sm mt-1">Socios vencidos</div>
    </div>
    {{-- Monthly Income --}}
    <div class="stat-card border-blue-500">
        <div class="text-3xl font-bold text-blue-600">${{ number_format($stats['monthly_income'] + $stats['field_income'], 0, ',', '.') }}</div>
        <div class="text-slate-500 text-sm mt-1">Ingresos del mes</div>
    </div>
    {{-- Net result --}}
    <div class="stat-card border-{{ $stats['net_result'] >= 0 ? 'emerald' : 'red' }}-500">
        <div class="text-3xl font-bold text-{{ $stats['net_result'] >= 0 ? 'emerald' : 'red' }}-600">
            ${{ number_format(abs($stats['net_result']), 0, ',', '.') }}
        </div>
        <div class="text-slate-500 text-sm mt-1">Resultado neto {{ $stats['net_result'] < 0 ? '(negativo)' : '' }}</div>
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="card p-4 text-center">
        <div class="text-2xl font-bold text-yellow-600">{{ $stats['upcoming_payments'] }}</div>
        <div class="text-slate-500 text-xs mt-1">Pagos próximos (7 días)</div>
    </div>
    <div class="card p-4 text-center">
        <div class="text-2xl font-bold text-red-600">{{ $stats['overdue_payments'] }}</div>
        <div class="text-slate-500 text-xs mt-1">Pagos vencidos</div>
    </div>
    <div class="card p-4 text-center">
        <div class="text-2xl font-bold text-slate-700">{{ $stats['reservations_today'] }}</div>
        <div class="text-slate-500 text-xs mt-1">Reservas hoy</div>
    </div>
    <div class="card p-4 text-center">
        <div class="text-2xl font-bold text-slate-600">${{ number_format($stats['monthly_expenses'], 0, ',', '.') }}</div>
        <div class="text-slate-500 text-xs mt-1">Gastos del mes</div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Due Soon --}}
    <div class="card p-5">
        <h2 class="font-semibold text-slate-700 mb-4 flex items-center gap-2">
            <span class="text-yellow-500">⚠️</span> Vencimientos próximos
        </h2>
        @forelse($dueSoon as $member)
            <a href="{{ route('gym.members.show', $member) }}" class="flex items-center justify-between py-2 border-b last:border-0 hover:bg-slate-50 rounded px-1">
                <div>
                    <div class="text-sm font-medium text-slate-700">{{ $member->full_name }}</div>
                    <div class="text-xs text-slate-400">{{ $member->currentPlan?->name }}</div>
                </div>
                <span class="text-xs text-yellow-600 font-medium">{{ $member->membership_expires_at?->format('d/m') }}</span>
            </a>
        @empty
            <p class="text-slate-400 text-sm">Sin vencimientos próximos.</p>
        @endforelse
        <div class="mt-3">
            <a href="{{ route('gym.members.index', ['status' => 'active']) }}" class="text-blue-600 text-xs hover:underline">Ver todos →</a>
        </div>
    </div>

    {{-- Recent Payments --}}
    <div class="card p-5">
        <h2 class="font-semibold text-slate-700 mb-4 flex items-center gap-2">
            <span>💰</span> Últimos pagos
        </h2>
        @forelse($recentPayments as $payment)
            <div class="flex items-center justify-between py-2 border-b last:border-0">
                <div>
                    <div class="text-sm font-medium text-slate-700">{{ $payment->member?->full_name }}</div>
                    <div class="text-xs text-slate-400">{{ $payment->paid_at?->format('d/m/Y') }}</div>
                </div>
                <span class="text-sm font-semibold text-green-600">${{ number_format($payment->amount, 0, ',', '.') }}</span>
            </div>
        @empty
            <p class="text-slate-400 text-sm">Sin pagos registrados.</p>
        @endforelse
        <div class="mt-3">
            <a href="{{ route('gym.payments.index') }}" class="text-blue-600 text-xs hover:underline">Ver todos →</a>
        </div>
    </div>

    {{-- Upcoming Reservations --}}
    <div class="card p-5">
        <h2 class="font-semibold text-slate-700 mb-4 flex items-center gap-2">
            <span>🗓️</span> Próximas reservas
        </h2>
        @forelse($upcomingReservations as $res)
            <a href="{{ route('gym.reservations.show', $res) }}" class="flex items-center justify-between py-2 border-b last:border-0 hover:bg-slate-50 rounded px-1">
                <div>
                    <div class="text-sm font-medium text-slate-700">{{ $res->customer_name }}</div>
                    <div class="text-xs text-slate-400">{{ $res->timeSlot?->label }}</div>
                </div>
                <span class="text-xs text-slate-600">{{ $res->reservation_date?->format('d/m') }}</span>
            </a>
        @empty
            <p class="text-slate-400 text-sm">Sin reservas próximas.</p>
        @endforelse
        <div class="mt-3">
            <a href="{{ route('gym.reservations.index') }}" class="text-blue-600 text-xs hover:underline">Ver calendario →</a>
        </div>
    </div>
</div>

{{-- Quick actions --}}
<div class="mt-6 grid grid-cols-2 sm:grid-cols-4 gap-3">
    <a href="{{ route('gym.members.create') }}" class="card p-4 text-center hover:shadow-md transition cursor-pointer">
        <div class="text-2xl mb-1">👤</div>
        <div class="text-sm font-medium text-slate-700">Nuevo socio</div>
    </a>
    <a href="{{ route('gym.payments.create') }}" class="card p-4 text-center hover:shadow-md transition cursor-pointer">
        <div class="text-2xl mb-1">💳</div>
        <div class="text-sm font-medium text-slate-700">Registrar pago</div>
    </a>
    <a href="{{ route('gym.reservations.create') }}" class="card p-4 text-center hover:shadow-md transition cursor-pointer">
        <div class="text-2xl mb-1">🗓️</div>
        <div class="text-sm font-medium text-slate-700">Nueva reserva</div>
    </a>
    <a href="{{ route('gym.expenses.create') }}" class="card p-4 text-center hover:shadow-md transition cursor-pointer">
        <div class="text-2xl mb-1">📉</div>
        <div class="text-sm font-medium text-slate-700">Registrar gasto</div>
    </a>
</div>
@endsection
