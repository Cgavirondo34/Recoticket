@extends('layouts.gym')

@section('title', 'Registrar gasto')

@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('gym.expenses.index') }}" class="text-slate-400 hover:text-slate-600">← Gastos</a>
    <h1 class="text-xl font-bold text-slate-800">Registrar gasto</h1>
</div>

<div class="max-w-lg card p-6">
    <form method="POST" action="{{ route('gym.expenses.store') }}" class="space-y-4">
        @csrf
        <div>
            <label class="form-label">Descripción *</label>
            <input type="text" name="description" value="{{ old('description') }}" required class="form-input" placeholder="Descripción del gasto">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="form-label">Monto ($) *</label>
                <input type="number" name="amount" value="{{ old('amount') }}" required min="0.01" step="0.01" class="form-input">
            </div>
            <div>
                <label class="form-label">Fecha *</label>
                <input type="date" name="expense_date" value="{{ old('expense_date', date('Y-m-d')) }}" required class="form-input">
            </div>
        </div>
        <div>
            <label class="form-label">Categoría</label>
            <select name="expense_category_id" class="form-input">
                <option value="">Sin categoría</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('expense_category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Método de pago</label>
            <select name="payment_method_id" class="form-input">
                <option value="">Sin especificar</option>
                @foreach($paymentMethods as $method)
                    <option value="{{ $method->id }}" {{ old('payment_method_id') == $method->id ? 'selected' : '' }}>{{ $method->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Unidad de negocio *</label>
            <select name="business_unit" required class="form-input">
                <option value="shared" {{ old('business_unit', 'shared') === 'shared' ? 'selected' : '' }}>Compartido / General</option>
                <option value="gym" {{ old('business_unit') === 'gym' ? 'selected' : '' }}>Gimnasio</option>
                <option value="field" {{ old('business_unit') === 'field' ? 'selected' : '' }}>Cancha</option>
            </select>
        </div>
        <div>
            <label class="form-label">Notas</label>
            <textarea name="notes" rows="2" class="form-input">{{ old('notes') }}</textarea>
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="btn-primary">Registrar gasto</button>
            <a href="{{ route('gym.expenses.index') }}" class="btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
@endsection
