@extends('layouts.app')

@section('title', 'Pago Exitoso')

@section('content')
<div class="max-w-md mx-auto text-center">
    <div class="bg-white rounded-2xl shadow-lg p-10">
        <div class="text-7xl mb-4">🎉</div>
        <h1 class="text-2xl font-bold text-gray-800 mb-2">¡Pago exitoso!</h1>
        <p class="text-gray-500 mb-6">Tu compra fue procesada correctamente. Ya podés ver tus entradas.</p>

        @if($order)
            <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-6 text-left">
                <p class="text-sm text-gray-600"><span class="font-semibold">Orden:</span> {{ $order->order_number }}</p>
                <p class="text-sm text-gray-600"><span class="font-semibold">Evento:</span> {{ $order->event?->title }}</p>
                <p class="text-sm text-gray-600"><span class="font-semibold">Total:</span> ${{ number_format($order->total, 2, ',', '.') }}</p>
            </div>
        @endif

        <div class="flex flex-col gap-3">
            <a href="{{ route('buyer.tickets.index') }}"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-xl font-semibold transition">
                Ver mis entradas
            </a>
            <a href="{{ route('home') }}" class="text-gray-400 text-sm hover:text-gray-600">Volver al inicio</a>
        </div>
    </div>
</div>
@endsection
