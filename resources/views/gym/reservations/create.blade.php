@extends('layouts.gym')

@section('title', 'Nueva reserva')

@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('gym.reservations.index') }}" class="text-slate-400 hover:text-slate-600">← Reservas</a>
    <h1 class="text-xl font-bold text-slate-800">Nueva reserva</h1>
</div>

<div class="max-w-lg card p-6">
    <form method="POST" action="{{ route('gym.reservations.store') }}" class="space-y-4" id="resForm">
        @csrf
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="form-label">Fecha *</label>
                <input type="date" name="reservation_date" value="{{ old('reservation_date', $selectedDate) }}" required class="form-input">
            </div>
            <div>
                <label class="form-label">Turno *</label>
                <select name="field_time_slot_id" required class="form-input">
                    <option value="">Seleccionar...</option>
                    @foreach($timeSlots as $slot)
                        <option value="{{ $slot->id }}"
                            {{ (old('field_time_slot_id') ?? $selectedSlot) == $slot->id ? 'selected' : '' }}
                            data-price="{{ $slot->price }}">
                            {{ $slot->label }} ({{ substr($slot->starts_at,0,5) }}–{{ substr($slot->ends_at,0,5) }}) — ${{ number_format($slot->price, 0, ',', '.') }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label class="form-label">Nombre del cliente *</label>
            <input type="text" name="customer_name" value="{{ old('customer_name') }}" required class="form-input" placeholder="Juan García">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="form-label">Teléfono</label>
                <input type="text" name="customer_phone" value="{{ old('customer_phone') }}" class="form-input" placeholder="+54 9 11...">
            </div>
            <div>
                <label class="form-label">WhatsApp</label>
                <input type="text" name="customer_whatsapp" value="{{ old('customer_whatsapp') }}" class="form-input" placeholder="+54 9 11...">
            </div>
        </div>

        {{-- Type --}}
        <div>
            <label class="form-label">Tipo de reserva</label>
            <div class="flex gap-4">
                <label class="flex items-center gap-2 text-sm cursor-pointer">
                    <input type="radio" name="type" value="occasional" {{ old('type', 'occasional') === 'occasional' ? 'checked' : '' }} onchange="toggleRecurring(false)">
                    Ocasional
                </label>
                <label class="flex items-center gap-2 text-sm cursor-pointer">
                    <input type="radio" name="type" value="recurring" {{ old('type') === 'recurring' ? 'checked' : '' }} onchange="toggleRecurring(true)">
                    Fija (recurrente)
                </label>
            </div>
        </div>

        <div id="recurringFields" class="{{ old('type') === 'recurring' ? '' : 'hidden' }} space-y-4 border-l-4 border-blue-200 pl-4">
            <div>
                <label class="form-label">Día de la semana</label>
                <select name="recurrence_day" class="form-input">
                    <option value="">Seleccionar día...</option>
                    @foreach(['monday'=>'Lunes','tuesday'=>'Martes','wednesday'=>'Miércoles','thursday'=>'Jueves','friday'=>'Viernes','saturday'=>'Sábado','sunday'=>'Domingo'] as $val => $label)
                        <option value="{{ $val }}" {{ old('recurrence_day') === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Hasta fecha (opcional)</label>
                <input type="date" name="recurring_until" value="{{ old('recurring_until') }}" class="form-input">
            </div>
        </div>

        <div>
            <label class="form-label">Monto ($)</label>
            <input type="number" name="amount" value="{{ old('amount') }}" min="0" step="0.01" class="form-input" placeholder="Auto desde turno">
        </div>
        <div>
            <label class="form-label">Notas</label>
            <textarea name="notes" rows="2" class="form-input">{{ old('notes') }}</textarea>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="btn-primary">Crear reserva</button>
            <a href="{{ route('gym.reservations.index') }}" class="btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
function toggleRecurring(show) {
    document.getElementById('recurringFields').classList.toggle('hidden', !show);
}
document.querySelector('select[name="field_time_slot_id"]').addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    const price = opt.dataset.price;
    if (price) document.querySelector('input[name="amount"]').value = price;
});
</script>
@endpush
@endsection
