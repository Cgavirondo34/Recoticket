@extends('layouts.gym')

@section('title', $member->full_name)

@section('content')
<div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('gym.members.index') }}" class="text-slate-400 hover:text-slate-600">← Socios</a>
        <h1 class="text-xl font-bold text-slate-800">{{ $member->full_name }}</h1>
        <span class="badge-{{ $member->status }}">
            {{ ['active' => 'Activo', 'expired' => 'Vencido', 'suspended' => 'Suspendido'][$member->status] }}
        </span>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('gym.payments.create', ['member_id' => $member->id]) }}" class="btn-primary">💰 Registrar pago</a>
        <a href="{{ route('gym.members.edit', $member) }}" class="btn-secondary">Editar</a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Profile card --}}
    <div class="card p-5 h-fit">
        <h2 class="font-semibold text-slate-700 mb-4">Datos personales</h2>
        <dl class="space-y-3 text-sm">
            <div class="flex justify-between">
                <dt class="text-slate-400">DNI</dt>
                <dd class="text-slate-700 font-medium">{{ $member->dni ?: '—' }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-slate-400">Teléfono</dt>
                <dd class="text-slate-700">{{ $member->phone ?: '—' }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-slate-400">WhatsApp</dt>
                <dd class="text-slate-700">{{ $member->whatsapp ?: '—' }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-slate-400">Email</dt>
                <dd class="text-slate-700 text-xs">{{ $member->email ?: '—' }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-slate-400">Nacimiento</dt>
                <dd class="text-slate-700">{{ $member->birth_date?->format('d/m/Y') ?: '—' }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-slate-400">Entrenador</dt>
                <dd class="text-slate-700">{{ $member->trainer?->full_name ?: 'Sin asignar' }}</dd>
            </div>
        </dl>

        @if($member->notes)
            <div class="mt-4 pt-4 border-t">
                <div class="text-xs text-slate-400 mb-1">Observaciones</div>
                <p class="text-sm text-slate-600">{{ $member->notes }}</p>
            </div>
        @endif
    </div>

    {{-- Right column --}}
    <div class="lg:col-span-2 space-y-5">
        {{-- Current Membership --}}
        <div class="card p-5">
            <h2 class="font-semibold text-slate-700 mb-3">Membresía actual</h2>
            @if($member->currentPlan)
                <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg">
                    <div>
                        <div class="font-medium text-slate-700">{{ $member->currentPlan->name }}</div>
                        <div class="text-sm text-slate-400">${{ number_format($member->currentPlan->price, 0, ',', '.') }} / {{ $member->currentPlan->duration_days }} días</div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-slate-500">Vence</div>
                        <div class="font-semibold {{ $member->membership_expires_at?->isPast() ? 'text-red-600' : 'text-slate-700' }}">
                            {{ $member->membership_expires_at?->format('d/m/Y') ?: '—' }}
                        </div>
                    </div>
                </div>
            @else
                <p class="text-slate-400 text-sm">Sin plan asignado.</p>
            @endif
        </div>

        {{-- Payment history --}}
        <div class="card p-5">
            <h2 class="font-semibold text-slate-700 mb-3">Historial de pagos</h2>
            @if($member->payments->count())
                <table class="w-full text-sm">
                    <thead class="border-b">
                        <tr>
                            <th class="pb-2 text-left text-slate-400 font-medium">Fecha</th>
                            <th class="pb-2 text-left text-slate-400 font-medium">Monto</th>
                            <th class="pb-2 text-left text-slate-400 font-medium">Método</th>
                            <th class="pb-2 text-left text-slate-400 font-medium">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($member->payments->sortByDesc('paid_at') as $payment)
                            <tr class="border-b last:border-0">
                                <td class="py-2 text-slate-500">{{ $payment->paid_at?->format('d/m/Y') }}</td>
                                <td class="py-2 font-medium text-slate-700">${{ number_format($payment->amount, 0, ',', '.') }}</td>
                                <td class="py-2 text-slate-500">{{ $payment->paymentMethod?->name ?: '—' }}</td>
                                <td class="py-2"><span class="badge-{{ $payment->status }}">{{ ucfirst($payment->status) }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-slate-400 text-sm">Sin pagos registrados.</p>
            @endif
        </div>

        {{-- Active Routines --}}
        <div class="card p-5">
            <h2 class="font-semibold text-slate-700 mb-3">Rutinas asignadas</h2>
            @php $activeAssignments = $member->routineAssignments->where('active', true); @endphp
            @if($activeAssignments->count())
                @foreach($activeAssignments as $assignment)
                    <div class="p-3 bg-slate-50 rounded-lg mb-2">
                        <div class="font-medium text-slate-700">{{ $assignment->routine?->name }}</div>
                        <div class="text-xs text-slate-400 mt-1">
                            Asignada: {{ $assignment->assigned_at?->format('d/m/Y') }}
                            @if($assignment->trainer_notes)
                                · {{ $assignment->trainer_notes }}
                            @endif
                        </div>
                    </div>
                @endforeach
            @else
                <p class="text-slate-400 text-sm">Sin rutinas asignadas.</p>
            @endif
        </div>
    </div>
</div>
@endsection
