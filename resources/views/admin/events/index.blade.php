@extends('layouts.app')

@section('title', 'Eventos — Admin')

@section('content')
<h1 class="text-2xl font-bold text-gray-800 mb-6">Gestión de Eventos</h1>

<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-400 uppercase text-xs">
            <tr>
                <th class="px-5 py-3 text-left">Título</th>
                <th class="px-5 py-3 text-left">Organizador</th>
                <th class="px-5 py-3 text-left">Categoría</th>
                <th class="px-5 py-3 text-left">Fecha</th>
                <th class="px-5 py-3 text-left">Estado</th>
                <th class="px-5 py-3">Acción</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($events as $event)
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-3 font-medium text-gray-700">{{ $event->title }}</td>
                <td class="px-5 py-3 text-gray-500">{{ $event->organizer?->name }}</td>
                <td class="px-5 py-3 text-gray-500">{{ $event->category?->name }}</td>
                <td class="px-5 py-3 text-gray-400 text-xs">{{ $event->start_date?->format('d/m/Y') }}</td>
                <td class="px-5 py-3">
                    <span class="text-xs px-2 py-0.5 rounded-full font-medium
                        {{ $event->status === 'published' ? 'bg-green-100 text-green-700' : '' }}
                        {{ $event->status === 'draft' ? 'bg-gray-100 text-gray-500' : '' }}
                        {{ $event->status === 'cancelled' ? 'bg-red-100 text-red-600' : '' }}">
                        {{ $event->status }}
                    </span>
                </td>
                <td class="px-5 py-3">
                    <form method="POST" action="{{ route('admin.events.publish', $event) }}">
                        @csrf
                        <button type="submit" class="text-xs px-3 py-1 rounded font-medium transition
                            {{ $event->status === 'published' ? 'bg-gray-100 hover:bg-gray-200 text-gray-600' : 'bg-green-600 hover:bg-green-700 text-white' }}">
                            {{ $event->status === 'published' ? 'Despublicar' : 'Publicar' }}
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $events->links() }}</div>
@endsection
