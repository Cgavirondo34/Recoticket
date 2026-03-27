@extends('layouts.app')

@section('title', 'Orden ' . $order->order_number)

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Orden <span class="font-mono text-indigo-600">{{ $order->order_number }}</span></h1>
        <span class="px-3 py-1 rounded-full text-sm font-medium
            {{ $order->status === 'approved' ? 'bg-green-100 text-green-700' : '' }}
            {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
            {{ $order->status === 'cancelled' ? 'bg-red-100 text-red-600' : '' }}">
            {{ ucfirst($order->status) }}
        </span>
    </div>

    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <h2 class="font-bold text-gray-700 mb-4">Evento: {{ $order->event?->title }}</h2>
        <table class="w-full text-sm">
            <thead class="text-gray-400 text-xs uppercase">
                <tr>
                    <th class="text-left pb-2">Tipo</th>
                    <th class="text-center pb-2">Cant.</th>
                    <th class="text-right pb-2">Precio unit.</th>
                    <th class="text-right pb-2">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($order->orderItems as $item)
                <tr>
                    <td class="py-2 text-gray-700">{{ $item->ticketType?->name }}</td>
                    <td class="py-2 text-center">{{ $item->quantity }}</td>
                    <td class="py-2 text-right">${{ number_format($item->unit_price, 2, ',', '.') }}</td>
                    <td class="py-2 text-right font-medium">${{ number_format($item->subtotal, 2, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="border-t-2 border-gray-200 text-sm">
                <tr>
                    <td colspan="3" class="pt-2 text-right text-gray-500">Subtotal</td>
                    <td class="pt-2 text-right">${{ number_format($order->subtotal, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="3" class="text-right text-gray-500">Cargo por servicio (5%)</td>
                    <td class="text-right">${{ number_format($order->fee, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="3" class="text-right font-bold text-gray-800">Total</td>
                    <td class="text-right font-bold text-indigo-700">${{ number_format($order->total, 2, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    @if($order->tickets->isNotEmpty())
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="font-bold text-gray-700 mb-4">🎟 Entradas</h2>
        <div class="space-y-2">
            @foreach($order->tickets as $ticket)
            <div class="flex items-center justify-between border border-gray-100 rounded-lg px-4 py-2">
                <div>
                    <span class="text-sm text-gray-700">{{ $ticket->ticketType?->name }}</span>
                    <span class="ml-2 font-mono text-xs text-gray-400">{{ substr($ticket->ticket_code, 0, 16) }}…</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs px-2 py-0.5 rounded-full
                        {{ $ticket->status === 'valid' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ $ticket->status }}
                    </span>
                    <a href="{{ route('buyer.tickets.show', $ticket) }}" class="text-indigo-600 text-xs hover:underline">Ver QR</a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @if($order->status === 'pending')
    <div class="mt-4">
        <a href="{{ route('payment.checkout', $order) }}"
            class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg font-medium transition">
            Continuar al pago →
        </a>
    </div>
    @endif

    <a href="{{ route('buyer.orders.index') }}" class="block text-sm text-gray-400 mt-4 hover:text-gray-600">← Volver a mis órdenes</a>
</div>
@endsection
