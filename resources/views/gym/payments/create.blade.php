@extends('layouts.gym')

@section('title', 'Registrar pago')

@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('gym.payments.index') }}" class="text-slate-400 hover:text-slate-600">← Pagos</a>
    <h1 class="text-xl font-bold text-slate-800">Registrar pago</h1>
</div>

<div class="max-w-lg card p-6">
    <form method="POST" action="{{ route('gym.payments.store') }}" class="space-y-4">
        @csrf
        <div>
            <label class="form-label">Socio *</label>
            <select name="member_id" required class="form-input">
                <option value="">Seleccionar socio...</option>
                @foreach($members as $m)
                    <option value="{{ $m->id }}" {{ (old('member_id') ?? $selectedMember?->id) == $m->id ? 'selected' : '' }}>
                        {{ $m->full_name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="form-label">Monto ($) *</label>
                <input type="number" name="amount" value="{{ old('amount') }}" required min="0.01" step="0.01" class="form-input" placeholder="5000">
            </div>
            <div>
                <label class="form-label">Fecha de pago *</label>
                <input type="date" name="paid_at" value="{{ old('paid_at', date('Y-m-d')) }}" required class="form-input">
            </div>
        </div>
        <div>
            <label class="form-label">Método de pago</label>
            <select name="payment_method_id" class="form-input">
                <option value="">Sin especificar</option>
                @foreach($paymentMethods as $method)
                    <option value="{{ $method->id }}" {{ old('payment_method_id') == $method->id ? 'selected' : '' }}>
                        {{ $method->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Referencia / Comprobante</label>
            <input type="text" name="reference" value="{{ old('reference') }}" class="form-input" placeholder="Nro de transferencia, recibo, etc.">
        </div>
        <div>
            <label class="form-label">Notas</label>
            <textarea name="notes" rows="2" class="form-input" placeholder="Notas opcionales">{{ old('notes') }}</textarea>
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="btn-primary">Registrar pago</button>
            <a href="{{ route('gym.payments.index') }}" class="btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
@endsection
