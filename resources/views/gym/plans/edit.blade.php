@extends('layouts.gym')

@section('title', 'Editar plan')

@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('gym.plans.index') }}" class="text-slate-400 hover:text-slate-600">← Planes</a>
    <h1 class="text-xl font-bold text-slate-800">Editar plan</h1>
</div>

<div class="max-w-lg card p-6">
    <form method="POST" action="{{ route('gym.plans.update', $plan) }}" class="space-y-4">
        @csrf @method('PUT')
        <div>
            <label class="form-label">Nombre *</label>
            <input type="text" name="name" value="{{ old('name', $plan->name) }}" required class="form-input">
        </div>
        <div>
            <label class="form-label">Descripción</label>
            <textarea name="description" rows="2" class="form-input">{{ old('description', $plan->description) }}</textarea>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="form-label">Precio ($) *</label>
                <input type="number" name="price" value="{{ old('price', $plan->price) }}" required min="0" step="0.01" class="form-input">
            </div>
            <div>
                <label class="form-label">Duración (días) *</label>
                <input type="number" name="duration_days" value="{{ old('duration_days', $plan->duration_days) }}" required min="1" class="form-input">
            </div>
        </div>
        <div class="flex items-center gap-2">
            <input type="checkbox" name="auto_renew_default" id="auto_renew_default" value="1" {{ old('auto_renew_default', $plan->auto_renew_default) ? 'checked' : '' }}>
            <label for="auto_renew_default" class="text-sm text-slate-600">Renovación automática por defecto</label>
        </div>
        <div class="flex items-center gap-2">
            <input type="checkbox" name="active" id="active" value="1" {{ old('active', $plan->active) ? 'checked' : '' }}>
            <label for="active" class="text-sm text-slate-600">Plan activo</label>
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="btn-primary">Guardar</button>
            <a href="{{ route('gym.plans.index') }}" class="btn-secondary">Cancelar</a>
            <form method="POST" action="{{ route('gym.plans.destroy', $plan) }}" onsubmit="return confirm('¿Eliminar este plan?')" class="inline">
                @csrf @method('DELETE')
                <button type="submit" class="btn-danger">Eliminar</button>
            </form>
        </div>
    </form>
</div>
@endsection
