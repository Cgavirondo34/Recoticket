@extends('layouts.gym')

@section('title', 'Editar reserva')

@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('gym.reservations.show', $reservation) }}" class="text-slate-400 hover:text-slate-600">← Reserva</a>
    <h1 class="text-xl font-bold text-slate-800">Editar reserva</h1>
</div>

<div class="max-w-lg card p-6">
    <form method="POST" action="{{ route('gym.reservations.update', $reservation) }}" class="space-y-4">
        @csrf @method('PUT')
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="form-label">Fecha *</label>
                <input type="date" name="reservation_date" value="{{ old('reservation_date', $reservation->reservation_date->format('Y-m-d')) }}" required class="form-input">
            </div>
            <div>
                <label class="form-label">Estado</label>
                <select name="status" class="form-input">
                    <option value="confirmed" {{ old('status', $reservation->status) === 'confirmed' ? 'selected' : '' }}>Confirmada</option>
                    <option value="pending" {{ old('status', $reservation->status) === 'pending' ? 'selected' : '' }}>Pendiente</option>
                    <option value="cancelled" {{ old('status', $reservation->status) === 'cancelled' ? 'selected' : '' }}>Cancelada</option>
                </select>
            </div>
        </div>
        <div>
            <label class="form-label">Nombre del cliente *</label>
            <input type="text" name="customer_name" value="{{ old('customer_name', $reservation->customer_name) }}" required class="form-input">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="form-label">Teléfono</label>
                <input type="text" name="customer_phone" value="{{ old('customer_phone', $reservation->customer_phone) }}" class="form-input">
            </div>
            <div>
                <label class="form-label">WhatsApp</label>
                <input type="text" name="customer_whatsapp" value="{{ old('customer_whatsapp', $reservation->customer_whatsapp) }}" class="form-input">
            </div>
        </div>
        <div>
            <label class="form-label">Monto ($)</label>
            <input type="number" name="amount" value="{{ old('amount', $reservation->amount) }}" min="0" step="0.01" class="form-input">
        </div>
        <div>
            <label class="form-label">Notas</label>
            <textarea name="notes" rows="2" class="form-input">{{ old('notes', $reservation->notes) }}</textarea>
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="btn-primary">Guardar cambios</button>
            <a href="{{ route('gym.reservations.show', $reservation) }}" class="btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
@endsection
