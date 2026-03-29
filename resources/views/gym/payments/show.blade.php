@extends('layouts.gym')

@section('title', 'Detalle de pago')

@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('gym.payments.index') }}" class="text-slate-400 hover:text-slate-600">← Pagos</a>
    <h1 class="text-xl font-bold text-slate-800">Pago #{{ $payment->id }}</h1>
    <span class="badge-{{ $payment->status }}">{{ ucfirst($payment->status) }}</span>
</div>

<div class="max-w-lg card p-6">
    <dl class="space-y-4 text-sm">
        <div class="flex justify-between border-b pb-3">
            <dt class="text-slate-400">Socio</dt>
            <dd class="font-medium text-slate-700">
                <a href="{{ route('gym.members.show', $payment->member) }}" class="hover:text-blue-600">
                    {{ $payment->member?->full_name }}
                </a>
            </dd>
        </div>
        <div class="flex justify-between border-b pb-3">
            <dt class="text-slate-400">Monto</dt>
            <dd class="text-xl font-bold text-green-600">${{ number_format($payment->amount, 2, ',', '.') }}</dd>
        </div>
        <div class="flex justify-between border-b pb-3">
            <dt class="text-slate-400">Fecha</dt>
            <dd class="text-slate-700">{{ $payment->paid_at?->format('d/m/Y') }}</dd>
        </div>
        <div class="flex justify-between border-b pb-3">
            <dt class="text-slate-400">Método</dt>
            <dd class="text-slate-700">{{ $payment->paymentMethod?->name ?: '—' }}</dd>
        </div>
        <div class="flex justify-between border-b pb-3">
            <dt class="text-slate-400">Plan</dt>
            <dd class="text-slate-700">{{ $payment->membership?->plan?->name ?: '—' }}</dd>
        </div>
        @if($payment->reference)
            <div class="flex justify-between border-b pb-3">
                <dt class="text-slate-400">Referencia</dt>
                <dd class="text-slate-700 font-mono text-xs">{{ $payment->reference }}</dd>
            </div>
        @endif
        @if($payment->mercadopago_id)
            <div class="flex justify-between border-b pb-3">
                <dt class="text-slate-400">ID Mercado Pago</dt>
                <dd class="text-slate-700 font-mono text-xs">{{ $payment->mercadopago_id }}</dd>
            </div>
        @endif
        @if($payment->notes)
            <div>
                <dt class="text-slate-400 mb-1">Notas</dt>
                <dd class="text-slate-600">{{ $payment->notes }}</dd>
            </div>
        @endif
    </dl>

    @if($payment->status === 'pending')
        <div class="mt-5 pt-5 border-t flex gap-3">
            <a href="{{ route('gym.payments.mercadopago', $payment) }}" class="btn-primary">
                💳 Pagar con Mercado Pago
            </a>
        </div>
    @endif
</div>
@endsection
