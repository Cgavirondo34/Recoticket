@extends('layouts.gym')

@section('title', 'Reserva — ' . $reservation->customer_name)

@section('content')
<div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('gym.reservations.index', ['date' => $reservation->reservation_date->toDateString()]) }}" class="text-slate-400 hover:text-slate-600">← Reservas</a>
        <h1 class="text-xl font-bold text-slate-800">{{ $reservation->customer_name }}</h1>
        <span class="badge-{{ $reservation->status }}">{{ ucfirst($reservation->status) }}</span>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('gym.reservations.edit', $reservation) }}" class="btn-secondary">Editar</a>
        <form method="POST" action="{{ route('gym.reservations.destroy', $reservation) }}" onsubmit="return confirm('¿Cancelar reserva?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn-danger">Cancelar</button>
        </form>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="card p-5">
        <h2 class="font-semibold text-slate-700 mb-4">Detalles</h2>
        <dl class="space-y-3 text-sm">
            <div class="flex justify-between border-b pb-2">
                <dt class="text-slate-400">Fecha</dt>
                <dd class="font-medium">{{ $reservation->reservation_date->isoFormat('dddd D [de] MMMM [de] YYYY') }}</dd>
            </div>
            <div class="flex justify-between border-b pb-2">
                <dt class="text-slate-400">Turno</dt>
                <dd class="font-medium">{{ $reservation->timeSlot?->label }} ({{ substr($reservation->timeSlot?->starts_at??'',0,5) }}–{{ substr($reservation->timeSlot?->ends_at??'',0,5) }})</dd>
            </div>
            <div class="flex justify-between border-b pb-2">
                <dt class="text-slate-400">Teléfono</dt>
                <dd>{{ $reservation->customer_phone ?: '—' }}</dd>
            </div>
            <div class="flex justify-between border-b pb-2">
                <dt class="text-slate-400">WhatsApp</dt>
                <dd>{{ $reservation->customer_whatsapp ?: '—' }}</dd>
            </div>
            <div class="flex justify-between border-b pb-2">
                <dt class="text-slate-400">Tipo</dt>
                <dd>{{ $reservation->type === 'recurring' ? '🔄 Fija' : 'Ocasional' }}</dd>
            </div>
            @if($reservation->type === 'recurring')
                <div class="flex justify-between border-b pb-2">
                    <dt class="text-slate-400">Día recurrente</dt>
                    <dd>{{ ucfirst($reservation->recurrence_day) }}</dd>
                </div>
            @endif
            <div class="flex justify-between border-b pb-2">
                <dt class="text-slate-400">Monto</dt>
                <dd class="font-semibold">${{ number_format($reservation->amount, 0, ',', '.') }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-slate-400">Estado de pago</dt>
                <dd><span class="badge-{{ $reservation->payment_status }}">{{ ['pending'=>'Pendiente','paid'=>'Pagado','partial'=>'Parcial'][$reservation->payment_status] }}</span></dd>
            </div>
        </dl>
        @if($reservation->notes)
            <div class="mt-4 pt-4 border-t text-sm text-slate-600">
                <span class="text-slate-400">Notas:</span> {{ $reservation->notes }}
            </div>
        @endif
    </div>

    {{-- Payments --}}
    <div class="card p-5">
        <h2 class="font-semibold text-slate-700 mb-4">Pagos registrados</h2>
        @if($reservation->payments->count())
            @foreach($reservation->payments as $pay)
                <div class="flex justify-between items-center py-2 border-b last:border-0 text-sm">
                    <div>
                        <div class="font-medium text-slate-700">${{ number_format($pay->amount, 0, ',', '.') }}</div>
                        <div class="text-xs text-slate-400">{{ $pay->paid_at?->format('d/m/Y') }} · {{ $pay->paymentMethod?->name }}</div>
                    </div>
                </div>
            @endforeach
        @else
            <p class="text-slate-400 text-sm mb-4">Sin pagos registrados.</p>
        @endif

        @if($reservation->payment_status !== 'paid')
            <div class="mt-4 pt-4 border-t">
                <h3 class="font-medium text-slate-700 mb-3 text-sm">Registrar pago</h3>
                <form method="POST" action="{{ route('gym.reservations.payment', $reservation) }}" class="space-y-3">
                    @csrf
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="form-label text-xs">Monto *</label>
                            <input type="number" name="amount" value="{{ $reservation->amount }}" required min="0.01" step="0.01" class="form-input">
                        </div>
                        <div>
                            <label class="form-label text-xs">Fecha *</label>
                            <input type="date" name="paid_at" value="{{ date('Y-m-d') }}" required class="form-input">
                        </div>
                    </div>
                    <div>
                        <label class="form-label text-xs">Método</label>
                        <select name="payment_method_id" class="form-input">
                            <option value="">Sin especificar</option>
                            @foreach($paymentMethods as $method)
                                <option value="{{ $method->id }}">{{ $method->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn-primary text-sm">Registrar pago</button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection
