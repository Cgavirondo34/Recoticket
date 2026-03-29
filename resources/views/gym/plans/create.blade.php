@extends('layouts.gym')

@section('title', 'Nuevo plan')

@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('gym.plans.index') }}" class="text-slate-400 hover:text-slate-600">← Planes</a>
    <h1 class="text-xl font-bold text-slate-800">Nuevo plan</h1>
</div>

<div class="max-w-lg card p-6">
    <form method="POST" action="{{ route('gym.plans.store') }}" class="space-y-4">
        @csrf
        <div>
            <label class="form-label">Nombre *</label>
            <input type="text" name="name" value="{{ old('name') }}" required class="form-input" placeholder="Plan mensual">
        </div>
        <div>
            <label class="form-label">Descripción</label>
            <textarea name="description" rows="2" class="form-input" placeholder="Descripción opcional">{{ old('description') }}</textarea>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="form-label">Precio ($) *</label>
                <input type="number" name="price" value="{{ old('price') }}" required min="0" step="0.01" class="form-input" placeholder="5000">
            </div>
            <div>
                <label class="form-label">Duración (días) *</label>
                <input type="number" name="duration_days" value="{{ old('duration_days', 30) }}" required min="1" class="form-input" placeholder="30">
            </div>
        </div>
        <div class="flex items-center gap-2">
            <input type="checkbox" name="auto_renew_default" id="auto_renew_default" value="1" {{ old('auto_renew_default') ? 'checked' : '' }}>
            <label for="auto_renew_default" class="text-sm text-slate-600">Renovación automática por defecto</label>
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="btn-primary">Crear plan</button>
            <a href="{{ route('gym.plans.index') }}" class="btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
@endsection
