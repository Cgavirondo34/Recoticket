@extends('layouts.gym')

@section('title', 'Planes de membresía')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-bold text-slate-800">Planes de membresía</h1>
    <a href="{{ route('gym.plans.create') }}" class="btn-primary">+ Nuevo plan</a>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
    @forelse($plans as $plan)
        <div class="card p-5 {{ !$plan->active ? 'opacity-60' : '' }}">
            <div class="flex items-start justify-between mb-3">
                <h3 class="font-semibold text-slate-700">{{ $plan->name }}</h3>
                @if(!$plan->active)
                    <span class="text-xs bg-slate-100 text-slate-500 px-2 py-1 rounded">Inactivo</span>
                @endif
            </div>
            <div class="text-3xl font-bold text-blue-600 mb-1">${{ number_format($plan->price, 0, ',', '.') }}</div>
            <div class="text-sm text-slate-400 mb-3">{{ $plan->duration_days }} días</div>
            @if($plan->description)
                <p class="text-sm text-slate-500 mb-3">{{ $plan->description }}</p>
            @endif
            <div class="flex items-center justify-between text-sm">
                <span class="text-slate-400">{{ $plan->members_count }} socios activos</span>
                <div class="flex gap-2">
                    <a href="{{ route('gym.plans.edit', $plan) }}" class="text-blue-600 hover:underline">Editar</a>
                </div>
            </div>
        </div>
    @empty
        <div class="lg:col-span-3 card p-8 text-center text-slate-400">
            No hay planes definidos.
            <a href="{{ route('gym.plans.create') }}" class="text-blue-600 hover:underline ml-1">Crear el primero →</a>
        </div>
    @endforelse
</div>
@endsection
