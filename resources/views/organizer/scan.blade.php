@extends('layouts.app')

@section('title', 'Escanear Entrada')

@section('content')
<div class="max-w-md mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">📷 Escanear Entrada</h1>

    @if(isset($result))
        <div class="mb-6 rounded-xl p-5 text-center
            {{ $result['result'] === 'valid' ? 'bg-green-50 border-2 border-green-400' : '' }}
            {{ $result['result'] === 'already_used' ? 'bg-yellow-50 border-2 border-yellow-400' : '' }}
            {{ in_array($result['result'], ['invalid', 'cancelled']) ? 'bg-red-50 border-2 border-red-400' : '' }}">
            <div class="text-4xl mb-2">
                @if($result['result'] === 'valid') ✅
                @elseif($result['result'] === 'already_used') ⚠️
                @else ❌
                @endif
            </div>
            <p class="font-semibold text-lg
                {{ $result['result'] === 'valid' ? 'text-green-700' : '' }}
                {{ $result['result'] === 'already_used' ? 'text-yellow-700' : '' }}
                {{ in_array($result['result'], ['invalid', 'cancelled']) ? 'text-red-700' : '' }}">
                {{ $result['message'] }}
            </p>
            @if(isset($result['ticket']))
                <div class="mt-3 text-sm text-gray-600">
                    <p>Evento: <strong>{{ $result['ticket']->event?->title }}</strong></p>
                </div>
            @endif
        </div>
    @endif

    <div class="bg-white rounded-xl shadow p-6">
        <form method="POST" action="{{ route('organizer.scan.post') }}" class="space-y-4">
            @csrf
            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded text-sm">
                    @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
                </div>
            @endif
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Código del ticket</label>
                <input type="text" name="ticket_code" autofocus
                    placeholder="Escaneá o ingresá el código..."
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <button type="submit"
                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-3 rounded-lg font-semibold transition">
                Validar entrada
            </button>
        </form>
    </div>

    <p class="text-center text-xs text-gray-400 mt-4">Podés usar un escáner QR o ingresar el código manualmente.</p>
</div>
@endsection
