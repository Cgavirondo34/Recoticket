@extends('layouts.gym')

@section('title', 'Nuevo socio')

@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('gym.members.index') }}" class="text-slate-400 hover:text-slate-600">← Socios</a>
    <h1 class="text-xl font-bold text-slate-800">Nuevo socio</h1>
</div>

<form method="POST" action="{{ route('gym.members.store') }}">
    @csrf
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Personal data --}}
        <div class="lg:col-span-2 card p-6">
            <h2 class="font-semibold text-slate-700 mb-4">Datos personales</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="form-label">Nombre completo *</label>
                    <input type="text" name="full_name" value="{{ old('full_name') }}" required class="form-input" placeholder="Juan García">
                </div>
                <div>
                    <label class="form-label">DNI / ID</label>
                    <input type="text" name="dni" value="{{ old('dni') }}" class="form-input" placeholder="12345678">
                </div>
                <div>
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-input" placeholder="juan@ejemplo.com">
                </div>
                <div>
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="form-input" placeholder="+54 9 11 1234-5678">
                </div>
                <div>
                    <label class="form-label">WhatsApp</label>
                    <input type="text" name="whatsapp" value="{{ old('whatsapp') }}" class="form-input" placeholder="+54 9 11 1234-5678">
                </div>
                <div>
                    <label class="form-label">Fecha de nacimiento</label>
                    <input type="date" name="birth_date" value="{{ old('birth_date') }}" class="form-input">
                </div>
                <div>
                    <label class="form-label">Entrenador asignado</label>
                    <select name="trainer_id" class="form-input">
                        <option value="">Sin asignar</option>
                        @foreach($trainers as $trainer)
                            <option value="{{ $trainer->id }}" {{ old('trainer_id') == $trainer->id ? 'selected' : '' }}>
                                {{ $trainer->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="form-label">Observaciones</label>
                    <textarea name="notes" rows="3" class="form-input" placeholder="Lesiones, restricciones, etc.">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Plan assignment --}}
        <div class="card p-6 h-fit">
            <h2 class="font-semibold text-slate-700 mb-4">Asignar plan</h2>
            <div class="space-y-4">
                <div>
                    <label class="form-label">Plan de membresía</label>
                    <select name="plan_id" id="planSelect" class="form-input">
                        <option value="">Sin plan</option>
                        @foreach($plans as $plan)
                            <option value="{{ $plan->id }}" {{ old('plan_id') == $plan->id ? 'selected' : '' }}>
                                {{ $plan->name }} — ${{ number_format($plan->price, 0, ',', '.') }} ({{ $plan->duration_days }} días)
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Fecha de inicio</label>
                    <input type="date" name="starts_at" value="{{ old('starts_at', date('Y-m-d')) }}" class="form-input">
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="auto_renew" id="auto_renew" value="1" {{ old('auto_renew') ? 'checked' : '' }} class="rounded">
                    <label for="auto_renew" class="text-sm text-slate-600">Renovación automática</label>
                </div>
            </div>

            <div class="mt-6 space-y-2">
                <button type="submit" class="btn-primary w-full justify-center">Crear socio</button>
                <a href="{{ route('gym.members.index') }}" class="btn-secondary w-full justify-center">Cancelar</a>
            </div>
        </div>
    </div>
</form>
@endsection
