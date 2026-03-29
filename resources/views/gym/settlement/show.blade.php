@extends('layouts.gym')

@section('title', 'Liquidación ' . $month . '/' . $year)

@section('content')
<div class="flex items-center justify-between mb-6 flex-wrap gap-3">
    <div class="flex items-center gap-3">
        <a href="{{ route('gym.settlement.index') }}" class="text-slate-400 hover:text-slate-600">← Liquidaciones</a>
        <h1 class="text-xl font-bold text-slate-800">
            Liquidación — {{ \Carbon\Carbon::create($year, $month)->isoFormat('MMMM YYYY') }}
        </h1>
        <span class="text-xs px-2 py-0.5 rounded {{ $settlement->status === 'confirmed' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
            {{ $settlement->status === 'confirmed' ? '✅ Confirmada' : '📝 Borrador' }}
        </span>
    </div>
    <div class="flex gap-2">
        @if($settlement->status === 'draft')
            <form method="POST" action="{{ route('gym.settlement.confirm', ['year' => $year, 'month' => $month]) }}">
                @csrf
                <button type="submit" class="btn-primary">✅ Confirmar liquidación</button>
            </form>
        @endif
        <form method="POST" action="{{ route('gym.settlement.generate') }}">
            @csrf
            <input type="hidden" name="year" value="{{ $year }}">
            <input type="hidden" name="month" value="{{ $month }}">
            <button type="submit" class="btn-secondary">🔄 Regenerar</button>
        </form>
    </div>
</div>

{{-- Summary cards --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    <div class="card p-4 border-l-4 border-blue-500">
        <div class="text-2xl font-bold text-blue-600">${{ number_format($settlement->gym_income, 0, ',', '.') }}</div>
        <div class="text-xs text-slate-400 mt-1">Ingresos gym</div>
    </div>
    <div class="card p-4 border-l-4 border-green-500">
        <div class="text-2xl font-bold text-green-600">${{ number_format($settlement->field_income, 0, ',', '.') }}</div>
        <div class="text-xs text-slate-400 mt-1">Ingresos cancha</div>
    </div>
    <div class="card p-4 border-l-4 border-red-500">
        <div class="text-2xl font-bold text-red-600">${{ number_format($settlement->total_expenses, 0, ',', '.') }}</div>
        <div class="text-xs text-slate-400 mt-1">Gastos totales</div>
    </div>
    <div class="card p-4 border-l-4 border-{{ $settlement->net_income >= 0 ? 'emerald' : 'red' }}-500">
        <div class="text-2xl font-bold text-{{ $settlement->net_income >= 0 ? 'emerald' : 'red' }}-600">
            ${{ number_format(abs($settlement->net_income), 0, ',', '.') }}
        </div>
        <div class="text-xs text-slate-400 mt-1">Resultado neto</div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Partner distributions --}}
    @if($settlement->partner_distributions)
        <div class="card p-5">
            <h2 class="font-semibold text-slate-700 mb-4">Distribución entre socios</h2>
            <table class="w-full text-sm">
                <thead class="border-b">
                    <tr>
                        <th class="pb-2 text-left text-slate-400 font-medium">Socio</th>
                        <th class="pb-2 text-right text-slate-400 font-medium">Gym</th>
                        <th class="pb-2 text-right text-slate-400 font-medium">Cancha</th>
                        <th class="pb-2 text-right text-slate-400 font-medium">Gastos</th>
                        <th class="pb-2 text-right text-slate-400 font-medium">Neto</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($settlement->partner_distributions as $dist)
                        <tr class="border-b last:border-0">
                            <td class="py-2 font-medium text-slate-700">{{ $dist['partner_name'] }}</td>
                            <td class="py-2 text-right text-slate-600">${{ number_format($dist['gym_income'], 0, ',', '.') }}</td>
                            <td class="py-2 text-right text-slate-600">${{ number_format($dist['field_income'], 0, ',', '.') }}</td>
                            <td class="py-2 text-right text-red-500">-${{ number_format($dist['expense_share'], 0, ',', '.') }}</td>
                            <td class="py-2 text-right font-bold text-{{ $dist['net_earning'] >= 0 ? 'emerald' : 'red' }}-600">
                                ${{ number_format(abs($dist['net_earning']), 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- Settlement items (expenses breakdown) --}}
    @if($settlement->items->count())
        <div class="card p-5">
            <h2 class="font-semibold text-slate-700 mb-4">Detalle de gastos</h2>
            <table class="w-full text-sm">
                <thead class="border-b">
                    <tr>
                        <th class="pb-2 text-left text-slate-400 font-medium">Concepto</th>
                        <th class="pb-2 text-right text-slate-400 font-medium">Monto</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($settlement->items as $item)
                        <tr class="border-b last:border-0">
                            <td class="py-1.5 text-slate-600">{{ $item->label }}</td>
                            <td class="py-1.5 text-right font-medium text-red-500">${{ number_format(abs($item->amount), 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

@if($settlement->notes)
    <div class="card p-4 mt-5">
        <h3 class="font-semibold text-slate-600 text-sm mb-2">Notas</h3>
        <p class="text-sm text-slate-600">{{ $settlement->notes }}</p>
    </div>
@endif
@endsection
