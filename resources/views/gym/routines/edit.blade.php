@extends('layouts.gym')

@section('title', 'Editar rutina')

@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('gym.routines.show', $routine) }}" class="text-slate-400 hover:text-slate-600">← {{ $routine->name }}</a>
    <h1 class="text-xl font-bold text-slate-800">Editar rutina</h1>
</div>

<form method="POST" action="{{ route('gym.routines.update', $routine) }}">
    @csrf @method('PUT')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 card p-6 space-y-4">
            <div>
                <label class="form-label">Nombre *</label>
                <input type="text" name="name" value="{{ old('name', $routine->name) }}" required class="form-input">
            </div>
            <div>
                <label class="form-label">Objetivo</label>
                <textarea name="goal" rows="2" class="form-input">{{ old('goal', $routine->goal) }}</textarea>
            </div>
            <div>
                <label class="form-label">Notas</label>
                <textarea name="notes" rows="2" class="form-input">{{ old('notes', $routine->notes) }}</textarea>
            </div>

            <div>
                <div class="flex items-center justify-between mb-3">
                    <label class="form-label mb-0">Ejercicios</label>
                    <button type="button" onclick="addExercise()" class="btn-secondary text-xs">+ Agregar</button>
                </div>
                <div id="exerciseList" class="space-y-2">
                    @foreach($routine->exercises as $ex)
                        <div class="flex flex-wrap gap-2 items-center border rounded-lg p-3 bg-slate-50">
                            <input type="hidden" name="exercises[{{ $loop->index }}][id]" value="{{ $ex->id }}">
                            <div class="flex-1 min-w-40">
                                <input type="text" value="{{ $ex->name }}" class="form-input text-sm" readonly>
                            </div>
                            <div class="flex gap-2">
                                <input type="number" name="exercises[{{ $loop->index }}][sets]" value="{{ $ex->pivot->sets }}" min="1" placeholder="Series" class="form-input text-sm w-16 text-center" required>
                                <input type="text" name="exercises[{{ $loop->index }}][reps]" value="{{ $ex->pivot->reps }}" placeholder="Reps" class="form-input text-sm w-20 text-center" required>
                            </div>
                            <input type="text" name="exercises[{{ $loop->index }}][notes]" value="{{ $ex->pivot->notes }}" placeholder="Notas..." class="form-input text-sm flex-1 min-w-32">
                            <button type="button" onclick="this.closest('div').remove()" class="text-red-400 hover:text-red-600 text-lg">×</button>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="card p-5 h-fit space-y-3">
            <button type="submit" class="btn-primary w-full justify-center">Guardar cambios</button>
            <a href="{{ route('gym.routines.show', $routine) }}" class="btn-secondary w-full justify-center">Cancelar</a>
            <form method="POST" action="{{ route('gym.routines.destroy', $routine) }}" onsubmit="return confirm('¿Eliminar esta rutina?')" class="block mt-2">
                @csrf @method('DELETE')
                <button type="submit" class="btn-danger w-full justify-center">Eliminar</button>
            </form>

            <div class="mt-4">
                <p class="text-xs text-slate-400 font-medium mb-2">Ejercicios disponibles</p>
                <input type="text" placeholder="Buscar..." class="form-input text-xs mb-2" oninput="filterExercises(this.value)">
                <div class="max-h-48 overflow-y-auto space-y-1">
                    @foreach($exercises as $ex)
                        <div class="ex-item cursor-pointer hover:bg-slate-50 rounded px-2 py-1 text-sm text-slate-700 flex justify-between"
                             data-name="{{ $ex->name }}"
                             onclick="addExerciseFromLib({{ $ex->id }}, '{{ addslashes($ex->name) }}')">
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
let exIndex = {{ $routine->exercises->count() }};

function addExercise(id='', name='') {
    const list = document.getElementById('exerciseList');
    const row = document.createElement('div');
    row.className = 'flex flex-wrap gap-2 items-center border rounded-lg p-3 bg-slate-50';
    row.innerHTML = `
        <input type="hidden" name="exercises[${exIndex}][id]" value="${id}" required>
        <div class="flex-1 min-w-40"><input type="text" value="${name}" class="form-input text-sm" readonly></div>
        <div class="flex gap-2">
            <input type="number" name="exercises[${exIndex}][sets]" value="3" min="1" class="form-input text-sm w-16 text-center" required>
            <input type="text" name="exercises[${exIndex}][reps]" value="10" class="form-input text-sm w-20 text-center" required>
        </div>
        <input type="text" name="exercises[${exIndex}][notes]" placeholder="Notas..." class="form-input text-sm flex-1 min-w-32">
        <button type="button" onclick="this.closest('div').remove()" class="text-red-400 text-lg">×</button>
    `;
    list.appendChild(row);
    exIndex++;
}
function addExerciseFromLib(id, name) { addExercise(id, name); }
function filterExercises(val) {
    const q = val.toLowerCase();
    document.querySelectorAll('.ex-item').forEach(el => {
        el.style.display = el.dataset.name.toLowerCase().includes(q) ? '' : 'none';
    });
}
</script>
@endpush
@endsection
