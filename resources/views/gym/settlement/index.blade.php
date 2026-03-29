@extends('layouts.gym')

@section('title', 'Liquidaciones')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-bold text-slate-800">Liquidaciones financieras</h1>
</div>

{{-- Generate form --}}
<div class="card p-5 mb-6">
    <h2 class="font-semibold text-slate-700 mb-3">Generar liquidación</h2>
    <form method="POST" action="{{ route('gym.settlement.generate') }}" class="flex flex-wrap gap-3 items-end">
        @csrf
        <div>
            <label class="form-label">Año</label>
            <input type="number" name="year" value="{{ date('Y') }}" min="2020" max="2099" class="form-input w-24">
        </div>
        <div>
            <label class="form-label">Mes</label>
            <select name="month" class="form-input">
                @foreach(range(1,12) as $m)
                    <option value="{{ $m }}" {{ $m == date('n') ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create(null,$m)->isoFormat('MMMM') }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn-primary">📊 Generar</button>
    </form>
</div>

{{-- Settlement list --}}
<div class="card overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 border-b">
            <tr>
                <th class="px-4 py-3 text-left text-slate-500 font-medium">Período</th>
                <th class="px-4 py-3 text-right text-slate-500 font-medium">Ingresos gym</th>
                <th class="px-4 py-3 text-right text-slate-500 font-medium">Ingresos cancha</th>
                <th class="px-4 py-3 text-right text-slate-500 font-medium">Gastos</th>
                <th class="px-4 py-3 text-right text-slate-500 font-medium">Resultado neto</th>
                <th class="px-4 py-3 text-left text-slate-500 font-medium">Estado</th>
                <th class="px-4 py-3 text-right text-slate-500 font-medium">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($settlements as $s)
                <tr class="border-b table-row">
                    <td class="px-4 py-3 font-medium text-slate-700">
                        {{ \Carbon\Carbon::create($s->year, $s->month)->isoFormat('MMMM YYYY') }}
                    </td>
                    <td class="px-4 py-3 text-right text-slate-600">${{ number_format($s->gym_income, 0, ',', '.') }}</td>
                    <td class="px-4 py-3 text-right text-slate-600">${{ number_format($s->field_income, 0, ',', '.') }}</td>
                    <td class="px-4 py-3 text-right text-red-500">${{ number_format($s->total_expenses, 0, ',', '.') }}</td>
                    <td class="px-4 py-3 text-right font-semibold {{ $s->net_income >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        ${{ number_format(abs($s->net_income), 0, ',', '.') }}
                        {{ $s->net_income < 0 ? '(negativo)' : '' }}
                    </td>
                    <td class="px-4 py-3">
                        <span class="text-xs px-2 py-0.5 rounded {{ $s->status === 'confirmed' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                            {{ $s->status === 'confirmed' ? 'Confirmada' : 'Borrador' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('gym.settlement.show', ['year' => $s->year, 'month' => $s->month]) }}" class="text-blue-600 hover:underline text-xs">Ver detalle</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-slate-400">
                        Sin liquidaciones generadas.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t">{{ $settlements->links() }}</div>
</div>
@endsection
