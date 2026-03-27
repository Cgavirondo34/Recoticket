@extends('layouts.app')

@section('title', 'Pago Fallido')

@section('content')
<div class="max-w-md mx-auto text-center">
    <div class="bg-white rounded-2xl shadow-lg p-10">
        <div class="text-7xl mb-4">😕</div>
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Pago no completado</h1>
        <p class="text-gray-500 mb-6">El pago no pudo ser procesado. Podés intentarlo nuevamente.</p>

        @if($order)
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6 text-left">
                <p class="text-sm text-gray-600"><span class="font-semibold">Orden:</span> {{ $order->order_number }}</p>
                <p class="text-sm text-gray-600"><span class="font-semibold">Evento:</span> {{ $order->event?->title }}</p>
                <p class="text-sm text-gray-600"><span class="font-semibold">Total:</span> ${{ number_format($order->total, 2, ',', '.') }}</p>
            </div>
            <a href="{{ route('payment.checkout', $order) }}"
                class="block bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-xl font-semibold transition mb-3">
                Reintentar pago
            </a>
        @endif

        <a href="{{ route('buyer.orders.index') }}" class="block text-gray-400 text-sm hover:text-gray-600">Ver mis órdenes</a>
        <a href="{{ route('home') }}" class="block text-gray-400 text-sm hover:text-gray-600 mt-2">Volver al inicio</a>
    </div>
</div>
@endsection
