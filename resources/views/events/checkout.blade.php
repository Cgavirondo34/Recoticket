@extends('layouts.app')

@section('title', 'Checkout — ' . $event->title)

@section('content')
<div class="max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-2">Comprar entradas</h1>
    <p class="text-gray-500 mb-6">{{ $event->title }} — {{ $event->start_date?->format('d/m/Y H:i') }}</p>

    @if($errors->any())
        <div class="mb-4 bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded">
            @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('checkout.store', $event->slug) }}" class="space-y-4">
        @csrf
        @forelse($event->ticketTypes as $index => $type)
        <div class="bg-white rounded-xl shadow p-5 flex items-center justify-between gap-4">
            <input type="hidden" name="items[{{ $index }}][ticket_type_id]" value="{{ $type->id }}">
            <div class="flex-1">
                <div class="font-semibold text-gray-700">{{ $type->name }}</div>
                @if($type->description)
                    <div class="text-gray-400 text-sm">{{ $type->description }}</div>
                @endif
                <div class="text-sm text-gray-500 mt-1">
                    @if($type->price == 0)
                        <span class="text-green-600 font-bold">Gratis</span>
                    @else
                        <span class="text-indigo-700 font-bold">${{ number_format($type->price, 0, ',', '.') }}</span> c/u
                    @endif
                    &nbsp;·&nbsp; {{ $type->available }} disponibles
                </div>
            </div>
            <div class="flex items-center gap-2">
                <label class="text-sm text-gray-500">Cantidad</label>
                <select name="items[{{ $index }}][quantity]"
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500"
                    {{ $type->available == 0 ? 'disabled' : '' }}>
                    @for($q = 0; $q <= min(10, $type->available); $q++)
                        <option value="{{ $q }}">{{ $q }}</option>
                    @endfor
                </select>
            </div>
        </div>
        @empty
            <p class="text-gray-500 text-center py-8">No hay tipos de entrada activos para este evento.</p>
        @endforelse

        @if($event->ticketTypes->isNotEmpty())
        <div class="bg-indigo-50 rounded-xl p-4 text-sm text-indigo-700">
            <span class="font-semibold">Nota:</span> Se aplica una tarifa de servicio del 5% sobre el subtotal.
        </div>
        <button type="submit"
            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-3 rounded-xl font-semibold text-base transition">
            Continuar al pago →
        </button>
        @endif
    </form>

    <a href="{{ route('events.show', $event->slug) }}" class="block text-center text-sm text-gray-400 mt-4 hover:text-gray-600">← Volver al evento</a>
</div>
@endsection
