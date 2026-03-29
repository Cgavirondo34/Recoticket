@extends('layouts.gym')

@section('title', 'Rutinas')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-bold text-slate-800">Rutinas de entrenamiento</h1>
    <a href="{{ route('gym.routines.create') }}" class="btn-primary">+ Nueva rutina</a>
</div>

<div class="card overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 border-b">
            <tr>
                <th class="px-4 py-3 text-left text-slate-500 font-medium">Nombre</th>
                <th class="px-4 py-3 text-left text-slate-500 font-medium hidden md:table-cell">Objetivo</th>
                <th class="px-4 py-3 text-left text-slate-500 font-medium">Socios asignados</th>
                <th class="px-4 py-3 text-left text-slate-500 font-medium">Estado</th>
                <th class="px-4 py-3 text-right text-slate-500 font-medium">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($routines as $routine)
                <tr class="border-b table-row">
                    <td class="px-4 py-3 font-medium text-slate-700">
                        <a href="{{ route('gym.routines.show', $routine) }}" class="hover:text-blue-600">{{ $routine->name }}</a>
                    </td>
                    <td class="px-4 py-3 text-slate-400 text-xs hidden md:table-cell">{{ Str::limit($routine->goal, 60) ?: '—' }}</td>
                    <td class="px-4 py-3 text-slate-500">{{ $routine->active_assignments_count }}</td>
                    <td class="px-4 py-3">
                        @if($routine->active)
                            <span class="badge-active">Activa</span>
                        @else
                            <span class="badge-suspended">Inactiva</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('gym.routines.show', $routine) }}" class="text-blue-600 hover:underline text-xs mr-2">Ver</a>
                        <a href="{{ route('gym.routines.edit', $routine) }}" class="text-slate-500 hover:underline text-xs">Editar</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-slate-400">
                        No hay rutinas. <a href="{{ route('gym.routines.create') }}" class="text-blue-600 hover:underline">Crear la primera →</a>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t">{{ $routines->links() }}</div>
</div>
@endsection
