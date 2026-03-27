@extends('layouts.app')

@section('title', 'Mis Órdenes')

@section('content')
<h1 class="text-2xl font-bold text-gray-800 mb-6">Mis Órdenes</h1>

@if($orders->isEmpty())
    <div class="text-center py-16 text-gray-400">
        <div class="text-5xl mb-4">📋</div>
        <p class="text-lg">No tenés órdenes aún.</p>
        <a href="{{ route('home') }}" class="mt-4 inline-block bg-indigo-600 text-white px-6 py-2.5 rounded-lg font-medium hover:bg-indigo-700 transition">Ver eventos</a>
    </div>
@else
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                <tr>
                    <th class="px-5 py-3 text-left">Orden</th>
                    <th class="px-5 py-3 text-left">Evento</th>
                    <th class="px-5 py-3 text-left">Total</th>
                    <th class="px-5 py-3 text-left">Estado</th>
                    <th class="px-5 py-3 text-left">Fecha</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($orders as $order)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-3 font-mono text-xs font-semibold text-gray-600">{{ $order->order_number }}</td>
                    <td class="px-5 py-3 font-medium text-gray-700">{{ $order->event?->title }}</td>
                    <td class="px-5 py-3 text-gray-700">${{ number_format($order->total, 2, ',', '.') }}</td>
                    <td class="px-5 py-3">
                        <span class="px-2 py-1 rounded-full text-xs font-medium
                            {{ $order->status === 'approved' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                            {{ $order->status === 'cancelled' ? 'bg-red-100 text-red-600' : '' }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-gray-400 text-xs">{{ $order->created_at->format('d/m/Y') }}</td>
                    <td class="px-5 py-3">
                        <a href="{{ route('buyer.orders.show', $order) }}" class="text-indigo-600 hover:underline text-xs">Ver</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $orders->links() }}</div>
@endif
@endsection
