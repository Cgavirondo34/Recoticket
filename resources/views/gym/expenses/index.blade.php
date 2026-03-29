@extends('layouts.gym')

@section('title', 'Gastos')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-bold text-slate-800">Gastos</h1>
    <a href="{{ route('gym.expenses.create') }}" class="btn-primary">+ Registrar gasto</a>
</div>

{{-- Month navigator --}}
<div class="card p-4 mb-5">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="form-label">Año</label>
            <input type="number" name="year" value="{{ $year }}" min="2020" max="2099" class="form-input w-24">
        </div>
        <div>
            <label class="form-label">Mes</label>
            <select name="month" class="form-input">
                @foreach(range(1,12) as $m)
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create(null,$m)->isoFormat('MMMM') }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Unidad</label>
            <select name="business_unit" class="form-input">
                <option value="">Todas</option>
                <option value="gym" {{ request('business_unit') === 'gym' ? 'selected' : '' }}>Gimnasio</option>
                <option value="field" {{ request('business_unit') === 'field' ? 'selected' : '' }}>Cancha</option>
                <option value="shared" {{ request('business_unit') === 'shared' ? 'selected' : '' }}>Compartido</option>
            </select>
        </div>
        <div>
            <label class="form-label">Categoría</label>
            <select name="category_id" class="form-input">
                <option value="">Todas</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn-primary">Filtrar</button>
    </form>
</div>

{{-- Summary cards --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-5">
    <div class="card p-4 border-l-4 border-blue-500">
        <div class="text-lg font-bold text-blue-600">${{ number_format($totals['gym'], 0, ',', '.') }}</div>
        <div class="text-xs text-slate-400 mt-1">Gimnasio</div>
    </div>
    <div class="card p-4 border-l-4 border-green-500">
        <div class="text-lg font-bold text-green-600">${{ number_format($totals['field'], 0, ',', '.') }}</div>
        <div class="text-xs text-slate-400 mt-1">Cancha</div>
    </div>
    <div class="card p-4 border-l-4 border-yellow-500">
        <div class="text-lg font-bold text-yellow-600">${{ number_format($totals['shared'], 0, ',', '.') }}</div>
        <div class="text-xs text-slate-400 mt-1">Compartido</div>
    </div>
    <div class="card p-4 border-l-4 border-red-500">
        <div class="text-lg font-bold text-red-600">${{ number_format($totals['total'], 0, ',', '.') }}</div>
        <div class="text-xs text-slate-400 mt-1">Total</div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-4 gap-5">
    {{-- Expense table --}}
    <div class="lg:col-span-3 card overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b">
                <tr>
                    <th class="px-4 py-3 text-left text-slate-500 font-medium">Descripción</th>
                    <th class="px-4 py-3 text-left text-slate-500 font-medium hidden md:table-cell">Categoría</th>
                    <th class="px-4 py-3 text-left text-slate-500 font-medium hidden md:table-cell">Unidad</th>
                    <th class="px-4 py-3 text-left text-slate-500 font-medium">Fecha</th>
                    <th class="px-4 py-3 text-right text-slate-500 font-medium">Monto</th>
                    <th class="px-4 py-3 text-right text-slate-500 font-medium">Acc.</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $expense)
                    <tr class="border-b table-row">
                        <td class="px-4 py-3 text-slate-700">{{ $expense->description }}</td>
                        <td class="px-4 py-3 text-slate-400 hidden md:table-cell text-xs">
                            @if($expense->category)
                                <span class="px-2 py-0.5 rounded" style="background: {{ $expense->category->color }}22; color: {{ $expense->category->color }}">
                                    {{ $expense->category->name }}
                                </span>
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-4 py-3 text-slate-400 hidden md:table-cell text-xs">
                            {{ ['gym'=>'Gimnasio','field'=>'Cancha','shared'=>'Compartido'][$expense->business_unit] }}
                        </td>
                        <td class="px-4 py-3 text-slate-400 text-xs">{{ $expense->expense_date->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-red-600">${{ number_format($expense->amount, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('gym.expenses.edit', $expense) }}" class="text-blue-600 hover:underline text-xs">Editar</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-slate-400">Sin gastos para este período.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t">{{ $expenses->links() }}</div>
    </div>

    {{-- Category breakdown --}}
    <div class="card p-4 h-fit">
        <h3 class="font-semibold text-slate-700 mb-3 text-sm">Por categoría</h3>
        @foreach($byCategory as $cat)
            <div class="flex items-center justify-between py-1.5 text-sm">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full" style="background: {{ $cat['color'] }}"></span>
                    <span class="text-slate-600">{{ $cat['label'] }}</span>
                </div>
                <span class="font-medium text-slate-700">${{ number_format($cat['amount'], 0, ',', '.') }}</span>
            </div>
        @endforeach
    </div>
</div>
@endsection
