@extends('layouts.gym')

@section('title', 'Socios')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-bold text-slate-800">Socios</h1>
    <a href="{{ route('gym.members.create') }}" class="btn-primary">+ Nuevo socio</a>
</div>

{{-- Filters --}}
<div class="card p-4 mb-5">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="form-label">Buscar</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nombre, DNI, teléfono..." class="form-input w-56">
        </div>
        <div>
            <label class="form-label">Estado</label>
            <select name="status" class="form-input">
                <option value="">Todos</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Activo</option>
                <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Vencido</option>
                <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspendido</option>
            </select>
        </div>
        <button type="submit" class="btn-primary">Filtrar</button>
        @if(request()->hasAny(['search','status']))
            <a href="{{ route('gym.members.index') }}" class="btn-secondary">Limpiar</a>
        @endif
    </form>
</div>

{{-- Table --}}
<div class="card overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 border-b">
            <tr>
                <th class="px-4 py-3 text-left text-slate-500 font-medium">Nombre</th>
                <th class="px-4 py-3 text-left text-slate-500 font-medium hidden md:table-cell">DNI</th>
                <th class="px-4 py-3 text-left text-slate-500 font-medium hidden md:table-cell">Teléfono</th>
                <th class="px-4 py-3 text-left text-slate-500 font-medium">Plan</th>
                <th class="px-4 py-3 text-left text-slate-500 font-medium">Vencimiento</th>
                <th class="px-4 py-3 text-left text-slate-500 font-medium">Estado</th>
                <th class="px-4 py-3 text-right text-slate-500 font-medium">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($members as $member)
                <tr class="border-b table-row">
                    <td class="px-4 py-3 font-medium text-slate-700">
                        <a href="{{ route('gym.members.show', $member) }}" class="hover:text-blue-600">
                            {{ $member->full_name }}
                        </a>
                    </td>
                    <td class="px-4 py-3 text-slate-500 hidden md:table-cell">{{ $member->dni ?: '—' }}</td>
                    <td class="px-4 py-3 text-slate-500 hidden md:table-cell">{{ $member->phone ?: '—' }}</td>
                    <td class="px-4 py-3 text-slate-500">{{ $member->currentPlan?->name ?: '—' }}</td>
                    <td class="px-4 py-3 text-slate-500">
                        @if($member->membership_expires_at)
                            <span class="{{ $member->membership_expires_at->isPast() ? 'text-red-600' : ($member->membership_expires_at->diffInDays() <= 3 ? 'text-yellow-600' : 'text-slate-500') }}">
                                {{ $member->membership_expires_at->format('d/m/Y') }}
                            </span>
                        @else
                            —
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <span class="badge-{{ $member->status }}">
                            {{ ['active' => 'Activo', 'expired' => 'Vencido', 'suspended' => 'Suspendido'][$member->status] }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('gym.members.show', $member) }}" class="text-blue-600 hover:underline text-xs mr-2">Ver</a>
                        <a href="{{ route('gym.members.edit', $member) }}" class="text-slate-500 hover:underline text-xs">Editar</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-slate-400">
                        No hay socios registrados.
                        <a href="{{ route('gym.members.create') }}" class="text-blue-600 hover:underline ml-1">Crear el primero →</a>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="px-4 py-3 border-t">
        {{ $members->links() }}
    </div>
</div>
@endsection
