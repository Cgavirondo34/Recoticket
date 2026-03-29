@extends('layouts.gym')

@section('title', 'Nueva rutina')

@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('gym.routines.index') }}" class="text-slate-400 hover:text-slate-600">← Rutinas</a>
    <h1 class="text-xl font-bold text-slate-800">Nueva rutina</h1>
</div>

<form method="POST" action="{{ route('gym.routines.store') }}" id="routineForm">
    @csrf
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 card p-6 space-y-4">
            <div>
                <label class="form-label">Nombre *</label>
                <input type="text" name="name" value="{{ old('name') }}" required class="form-input" placeholder="Rutina A — Empuje">
            </div>
            <div>
                <label class="form-label">Objetivo</label>
                <textarea name="goal" rows="2" class="form-input" placeholder="Descripción del objetivo...">{{ old('goal') }}</textarea>
            </div>
            <div>
                <label class="form-label">Notas generales</label>
                <textarea name="notes" rows="2" class="form-input" placeholder="Notas opcionales...">{{ old('notes') }}</textarea>
            </div>

            {{-- Exercise list --}}
            <div>
                <div class="flex items-center justify-between mb-3">
                    <label class="form-label mb-0">Ejercicios</label>
                    <button type="button" onclick="addExercise()" class="btn-secondary text-xs">+ Agregar ejercicio</button>
                </div>
                <div id="exerciseList" class="space-y-2">
                    {{-- Exercise rows injected by JS --}}
                </div>
            </div>
        </div>

        <div class="card p-5 h-fit">
            <button type="submit" class="btn-primary w-full justify-center">Crear rutina</button>
            <a href="{{ route('gym.routines.index') }}" class="btn-secondary w-full justify-center mt-2">Cancelar</a>

            <div class="mt-6">
                <p class="text-xs text-slate-400 font-medium mb-2">Ejercicios disponibles</p>
                <input type="text" id="exSearch" placeholder="Buscar ejercicio..." class="form-input text-xs" oninput="filterExercises(this.value)">
                <div id="exLibrary" class="mt-2 max-h-64 overflow-y-auto space-y-1">
                    @foreach($exercises as $ex)
                        <div class="ex-item cursor-pointer hover:bg-slate-50 rounded px-2 py-1.5 text-sm text-slate-700 flex items-center justify-between"
                             data-name="{{ $ex->name }}" data-id="{{ $ex->id }}" data-group="{{ $ex->muscle_group }}"
                             onclick="addExerciseFromLib({{ $ex->id }}, '{{ addslashes($ex->name) }}', '{{ addslashes($ex->muscle_group ?? '') }}')">
                            <span>{{ $ex->name }}</span>
                            <span class="text-xs text-slate-400">{{ $ex->muscle_group }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
let exIndex = 0;

function addExercise(id='', name='', group='') {
    const list = document.getElementById('exerciseList');
    const row = document.createElement('div');
    row.className = 'flex flex-wrap gap-2 items-center border rounded-lg p-3 bg-slate-50';
    row.innerHTML = `
        <input type="hidden" name="exercises[${exIndex}][id]" value="${id}" required class="ex-id-input w-0 opacity-0 absolute">
        <div class="flex-1 min-w-40">
            <input type="text" placeholder="Ejercicio" value="${name}" class="form-input text-sm ex-name-display" readonly
                onclick="promptExercise(${exIndex})" style="cursor:pointer">
        </div>
        <div class="flex gap-2">
            <input type="number" name="exercises[${exIndex}][sets]" value="3" min="1" placeholder="Series" class="form-input text-sm w-16 text-center" required>
            <input type="text" name="exercises[${exIndex}][reps]" value="10" placeholder="Reps" class="form-input text-sm w-20 text-center" required>
        </div>
        <input type="text" name="exercises[${exIndex}][notes]" placeholder="Notas..." class="form-input text-sm flex-1 min-w-32">
        <button type="button" onclick="this.closest('div').remove()" class="text-red-400 hover:text-red-600 text-lg leading-none">×</button>
    `;
    list.appendChild(row);
    if (id) row.querySelector('.ex-id-input').value = id;
    exIndex++;
}

function addExerciseFromLib(id, name, group) {
    addExercise(id, name, group);
}

function filterExercises(val) {
    const q = val.toLowerCase();
    document.querySelectorAll('.ex-item').forEach(el => {
        el.style.display = el.dataset.name.toLowerCase().includes(q) ? '' : 'none';
    });
}
</script>
@endpush
@endsection
