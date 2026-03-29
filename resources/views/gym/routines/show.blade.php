@extends('layouts.gym')

@section('title', $routine->name)

@section('content')
<div class="flex items-center justify-between mb-6 flex-wrap gap-3">
    <div class="flex items-center gap-3">
        <a href="{{ route('gym.routines.index') }}" class="text-slate-400 hover:text-slate-600">← Rutinas</a>
        <h1 class="text-xl font-bold text-slate-800">{{ $routine->name }}</h1>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('gym.routines.edit', $routine) }}" class="btn-secondary">Editar</a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Routine details --}}
    <div class="lg:col-span-2 space-y-5">
        @if($routine->goal)
            <div class="card p-4">
                <h3 class="font-semibold text-slate-600 text-sm mb-2">Objetivo</h3>
                <p class="text-slate-700">{{ $routine->goal }}</p>
            </div>
        @endif

        <div class="card p-5">
            <h2 class="font-semibold text-slate-700 mb-4">Ejercicios</h2>
            @if($routine->exercises->count())
                <table class="w-full text-sm">
                    <thead class="border-b">
                        <tr>
                            <th class="pb-2 text-left text-slate-400 font-medium">#</th>
                            <th class="pb-2 text-left text-slate-400 font-medium">Ejercicio</th>
                            <th class="pb-2 text-left text-slate-400 font-medium">Grupos</th>
                            <th class="pb-2 text-center text-slate-400 font-medium">Series</th>
                            <th class="pb-2 text-center text-slate-400 font-medium">Reps</th>
                            <th class="pb-2 text-left text-slate-400 font-medium">Notas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($routine->exercises as $ex)
                            <tr class="border-b last:border-0">
                                <td class="py-2 text-slate-300 text-xs">{{ $loop->iteration }}</td>
                                <td class="py-2 font-medium text-slate-700">{{ $ex->name }}</td>
                                <td class="py-2 text-slate-400 text-xs">{{ $ex->muscle_group ?: '—' }}</td>
                                <td class="py-2 text-center text-slate-600">{{ $ex->pivot->sets }}</td>
                                <td class="py-2 text-center text-slate-600">{{ $ex->pivot->reps }}</td>
                                <td class="py-2 text-slate-400 text-xs">{{ $ex->pivot->notes ?: '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-slate-400 text-sm">Sin ejercicios asignados.</p>
            @endif
        </div>

        {{-- Active assignments --}}
        <div class="card p-5">
            <h2 class="font-semibold text-slate-700 mb-4">Socios con esta rutina</h2>
            @php $active = $routine->assignments->where('active', true); @endphp
            @if($active->count())
                <table class="w-full text-sm">
                    <tbody>
                        @foreach($active as $asgmt)
                            <tr class="border-b last:border-0">
                                <td class="py-2">
                                    <a href="{{ route('gym.members.show', $asgmt->member) }}" class="font-medium text-slate-700 hover:text-blue-600">
                                        {{ $asgmt->member?->full_name }}
                                    </a>
                                </td>
                                <td class="py-2 text-xs text-slate-400">Desde {{ $asgmt->assigned_at?->format('d/m/Y') }}</td>
                                <td class="py-2 text-xs text-slate-400">{{ $asgmt->trainer_notes }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-slate-400 text-sm">Sin socios asignados actualmente.</p>
            @endif
        </div>
    </div>

    {{-- Assign form --}}
    <div class="card p-5 h-fit">
        <h2 class="font-semibold text-slate-700 mb-4">Asignar a socio</h2>
        <form method="POST" action="{{ route('gym.routines.assign', $routine) }}" class="space-y-3">
            @csrf
            <div>
                <label class="form-label text-xs">Socio *</label>
                <select name="member_id" required class="form-input">
                    <option value="">Seleccionar...</option>
                    @foreach($members as $m)
                        <option value="{{ $m->id }}">{{ $m->full_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label text-xs">Fecha de inicio *</label>
                <input type="date" name="assigned_at" value="{{ date('Y-m-d') }}" required class="form-input">
            </div>
            <div>
                <label class="form-label text-xs">Hasta (opcional)</label>
                <input type="date" name="ends_at" class="form-input">
            </div>
            <div>
                <label class="form-label text-xs">Notas del entrenador</label>
                <textarea name="trainer_notes" rows="2" class="form-input" placeholder="Observaciones..."></textarea>
            </div>
            <button type="submit" class="btn-primary w-full justify-center">Asignar</button>
        </form>
    </div>
</div>
@endsection
