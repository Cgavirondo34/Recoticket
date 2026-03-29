@extends('layouts.gym')

@section('title', 'Editar socio')

@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('gym.members.show', $member) }}" class="text-slate-400 hover:text-slate-600">← {{ $member->full_name }}</a>
    <h1 class="text-xl font-bold text-slate-800">Editar socio</h1>
</div>

<form method="POST" action="{{ route('gym.members.update', $member) }}">
    @csrf @method('PUT')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 card p-6">
            <h2 class="font-semibold text-slate-700 mb-4">Datos personales</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="form-label">Nombre completo *</label>
                    <input type="text" name="full_name" value="{{ old('full_name', $member->full_name) }}" required class="form-input">
                </div>
                <div>
                    <label class="form-label">DNI</label>
                    <input type="text" name="dni" value="{{ old('dni', $member->dni) }}" class="form-input">
                </div>
                <div>
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="{{ old('email', $member->email) }}" class="form-input">
                </div>
                <div>
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="phone" value="{{ old('phone', $member->phone) }}" class="form-input">
                </div>
                <div>
                    <label class="form-label">WhatsApp</label>
                    <input type="text" name="whatsapp" value="{{ old('whatsapp', $member->whatsapp) }}" class="form-input">
                </div>
                <div>
                    <label class="form-label">Fecha de nacimiento</label>
                    <input type="date" name="birth_date" value="{{ old('birth_date', $member->birth_date?->format('Y-m-d')) }}" class="form-input">
                </div>
                <div>
                    <label class="form-label">Entrenador</label>
                    <select name="trainer_id" class="form-input">
                        <option value="">Sin asignar</option>
                        @foreach($trainers as $trainer)
                            <option value="{{ $trainer->id }}" {{ old('trainer_id', $member->trainer_id) == $trainer->id ? 'selected' : '' }}>
                                {{ $trainer->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Estado</label>
                    <select name="status" class="form-input">
                        <option value="active" {{ old('status', $member->status) === 'active' ? 'selected' : '' }}>Activo</option>
                        <option value="expired" {{ old('status', $member->status) === 'expired' ? 'selected' : '' }}>Vencido</option>
                        <option value="suspended" {{ old('status', $member->status) === 'suspended' ? 'selected' : '' }}>Suspendido</option>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="form-label">Observaciones</label>
                    <textarea name="notes" rows="3" class="form-input">{{ old('notes', $member->notes) }}</textarea>
                </div>
            </div>
        </div>

        <div class="card p-6 h-fit space-y-3">
            <button type="submit" class="btn-primary w-full justify-center">Guardar cambios</button>
            <a href="{{ route('gym.members.show', $member) }}" class="btn-secondary w-full justify-center">Cancelar</a>
            <form method="POST" action="{{ route('gym.members.destroy', $member) }}" onsubmit="return confirm('¿Eliminar socio?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn-danger w-full justify-center mt-2">Eliminar socio</button>
            </form>
        </div>
    </div>
</form>
@endsection
