@extends('layouts.gym')

@section('title', 'Pagos')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-bold text-slate-800">Pagos</h1>
    <a href="{{ route('gym.payments.create') }}" class="btn-primary">+ Registrar pago</a>
</div>

{{-- Filters --}}
<div class="card p-4 mb-5">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="form-label">Estado</label>
            <select name="status" class="form-input">
                <option value="">Todos</option>
                <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Pagado</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pendiente</option>
                <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Vencido</option>
            </select>
        </div>
        <div>
            <label class="form-label">Socio</label>
            <select name="member_id" class="form-input">
                <option value="">Todos</option>
                @foreach($members as $m)
                    <option value="{{ $m->id }}" {{ request('member_id') == $m->id ? 'selected' : '' }}>{{ $m->full_name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn-primary">Filtrar</button>
        @if(request()->hasAny(['status','member_id']))
            <a href="{{ route('gym.payments.index') }}" class="btn-secondary">Limpiar</a>
        @endif
    </form>
</div>

<div class="card overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 border-b">
            <tr>
                <th class="px-4 py-3 text-left text-slate-500 font-medium">Socio</th>
                <th class="px-4 py-3 text-left text-slate-500 font-medium">Fecha</th>
                <th class="px-4 py-3 text-left text-slate-500 font-medium">Monto</th>
                <th class="px-4 py-3 text-left text-slate-500 font-medium hidden md:table-cell">Método</th>
                <th class="px-4 py-3 text-left text-slate-500 font-medium">Estado</th>
                <th class="px-4 py-3 text-right text-slate-500 font-medium">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payments as $payment)
                <tr class="border-b table-row">
                    <td class="px-4 py-3 font-medium text-slate-700">
                        <a href="{{ route('gym.members.show', $payment->member) }}" class="hover:text-blue-600">
                            {{ $payment->member?->full_name }}
                        </a>
                    </td>
                    <td class="px-4 py-3 text-slate-500">{{ $payment->paid_at?->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 font-semibold text-green-600">${{ number_format($payment->amount, 0, ',', '.') }}</td>
                    <td class="px-4 py-3 text-slate-400 hidden md:table-cell">{{ $payment->paymentMethod?->name ?: '—' }}</td>
                    <td class="px-4 py-3"><span class="badge-{{ $payment->status }}">{{ ucfirst($payment->status) }}</span></td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('gym.payments.show', $payment) }}" class="text-blue-600 hover:underline text-xs">Ver</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-slate-400">
                        No hay pagos registrados.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t">{{ $payments->links() }}</div>
</div>
@endsection
